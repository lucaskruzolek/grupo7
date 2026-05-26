<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Coleccion;
use App\Models\Color;
use App\Models\Talle;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Muestra el catálogo unificado para la tienda virtual.
     * Agrupa las filas por 'sku_base' para que el cliente vea un modelo único por tarjeta.
     */
public function index(Request $request)
{
    // Iniciamos la consulta base unificada por SKU_BASE
    $query = Producto::select('sku_base', 'nombre', 'precio', 'categoria_id', 'marca_id', 'tipo_mascota')
        ->with(['categoria', 'marca', 'imagenPortada'])
        ->groupBy('sku_base', 'nombre', 'precio', 'categoria_id', 'marca_id', 'tipo_mascota');

    // --- SISTEMA DE FILTRADO DINÁMICO ---

    // Filtro por Tipo de Mascota (Perro / Gato / Ambos)
    if ($request->has('mascota') && $request->mascota != '') {
        $query->where('tipo_mascota', $request->mascota);
    }

    // Filtro por Categoría (Ej: Buzos, Accesorios)
    if ($request->has('categoria') && $request->categoria != '') {
        $query->where('categoria_id', $request->categoria);
    }

    // Ejecutamos la consulta con los filtros aplicados
    $productos = $query->get();

    // Traemos las categorías del sistema para poder armar las selectores de filtro en la vista
    $categorias = \App\Models\Categoria::all();

    // Retornamos la vista del mostrador pasándole los productos filtrados y las categorías
    return view('frontend.productos', compact('productos', 'categorias'));
}

    /**
     * Formulario de creación de productos (Panel de Administración).
     */
    public function create()
    {
        $categorias = Categoria::all();
        $marcas = Marca::all();
        $colecciones = Coleccion::all();
        $colores = Color::all();
        $talles = Talle::all();

        return view('productos.create', compact('categorias', 'marcas', 'colecciones', 'colores', 'talles'));
    }

    /**
     * Almacena el producto y sus múltiples variantes físicas (talles) de forma atómica.
     */
    public function store(Request $request)
    {
        // Validamos la información descriptiva y comercial básica
        $request->validate([
            'nombre'        => 'required|string|max:150',
            'sku_base'      => 'required|string|max:50',
            'categoria_id'  => 'required|exists:categorias,id',
            'marca_id'      => 'required|exists:marcas,id',
            'coleccion_id'  => 'nullable|exists:colecciones,id',
            'color_id'      => 'required|exists:colores,id',
            'tipo_mascota'  => 'required|in:perro,gato,ambos',
            'precio'        => 'required|numeric|min:0',
            
            // Validamos que envíe al menos un talle con su stock
            'variantes'     => 'required|array|min:1',
            'variantes.*.talle_id' => 'required|exists:talles,id',
            'variantes.*.stock'    => 'required|integer|min:0',
            
            // Validamos las imágenes
            'imagenes'      => 'nullable|array',
            'imagenes.*'    => 'url' // O 'image|mimes:jpeg,png' si hacen uploads físicos
        ]);

        // Iniciamos una transacción de Base de Datos para asegurar que no queden datos huérfanos si algo falla
        DB::beginTransaction();

        try {
            $skuBase = strtoupper($request->sku_base);

            // 1. Guardar cada variante de talle como una fila única en 'productos'
            foreach ($request->variantes as $variante) {
                
                // Buscamos el nombre corto del talle (Ej: "S", "M") para armar el SKU final de inventario
                $talleNom = Talle::find($variante['talle_id'])->nombre;
                $skuVariante = $skuBase . '-' . strtoupper($talleNom);

                Producto::create([
                    'categoria_id' => $request->categoria_id,
                    'marca_id'     => $request->marca_id,
                    'coleccion_id' => $request->coleccion_id,
                    'color_id'     => $request->color_id,
                    'talle_id'     => $variante['talle_id'],
                    'nombre'       => $request->nombre,
                    'descripcion'  => $request->descripcion,
                    'tipo_mascota' => $request->tipo_mascota,
                    'sku_base'     => $skuBase,
                    'sku'          => $skuVariante,
                    'stock'        => $variante['stock'],
                    'stock_minimo' => $request->stock_minimo ?? 2,
                    'precio'       => $request->precio,
                ]);
            }

            // 2. Guardar las URLs de las imágenes asociándolas al grupo común (sku_base)
            if ($request->has('imagenes')) {
                foreach ($request->imagenes as $index => $url) {
                    ProductoImagen::create([
                        'sku_base' => $skuBase,
                        'url'      => $url,
                        'orden'    => $index + 1 // El primero indexado será orden = 1 (Portada)
                    ]);
                }
            }

            DB::commit(); // Si todo salió bien, guardamos definitivamente en disco
            return redirect()->route('productos.index')->with('exito', 'Prenda y variantes registradas correctamente.');

        } catch (\Exception $e) {
            DB::rollBack(); // Si saltó un error, deshacemos todo para no romper el stock
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Detalle de un producto en la tienda virtual.
     * Busca todas las variantes físicas que comparten el mismo 'sku_base' para renderizar los talles disponibles.
     */
    public function show($sku_base)
    {
        // Traemos el producto base para la descripción
        $productoBase = Producto::where('sku_base', $sku_base)->with(['categoria', 'marca', 'color'])->firstOrFail();

        // Traemos todas las variantes de talle que tengan stock disponible
        $variantesDisponibles = Producto::where('sku_base', $sku_base)
            ->with('talle')
            ->where('stock', '>', 0)
            ->get();

        // Traemos el carrusel de imágenes asociadas
        $imagenes = ProductoImagen::where('sku_base', $sku_base)->orderBy('orden', 'asc')->get();

        return view('productos.show', compact('productoBase', 'variantesDisponibles', 'imagenes'));
    }

    /**
     * Eliminación de un modelo completo (Baja lógica de todas sus variantes).
     */
    public function destroy($sku_base)
    {
        // El softdeletes se encargará de ocultar todas las filas que compartan el código de modelo
        Producto::where('sku_base', $sku_base)->delete();

        return redirect()->route('productos.index')->with('exito', 'El catálogo de este artículo fue dado de baja.');
    }
}