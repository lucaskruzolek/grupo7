<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
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
        // 1. Iniciamos la consulta unificada por SKU_BASE
        $query = Producto::select('sku_base', 'nombre', 'precio', 'categoria_id', 'tipo_mascota', 'coleccion_id', DB::raw('MAX(sku_color) as sku_color'))
            ->with(['categoria', 'imagenPortada']) // Mantenemos relaciones limpias
            ->groupBy('sku_base', 'nombre', 'precio', 'categoria_id', 'tipo_mascota', 'coleccion_id');

        // --- SISTEMA DE FILTRADO DINÁMICO ---

        // Filtro por Tipo de Mascota (Perro / Gato / Ambos)
        if ($request->has('mascota') && $request->mascota != '') {
            $query->where('tipo_mascota', $request->mascota);
        }

        // ─── FILTRO AVANZADO: Categorías y Subcategorías (parent_id) ───
        if ($request->has('categoria') && $request->categoria != '') {
            $categoriaId = $request->categoria;

            // Consultamos si el ID seleccionado es padre de otras subcategorías
            $subcategoriasIds = Categoria::where('parent_id', $categoriaId)->pluck('id');

            if ($subcategoriasIds->isNotEmpty()) {
                // CASO A: Es categoría Padre (ej: Seleccionó "Ropa")
                // Filtramos por todas sus subcategorías hijas (ej: Buzos, Suéteres)
                $query->whereIn('categoria_id', $subcategoriasIds);
            } else {
                // CASO B: Es una subcategoría directa (ej: Seleccionó "Juguetes" directamente)
                // Filtramos únicamente por ese ID específico
                $query->where('categoria_id', $categoriaId);
            }
        }

        // Filtro por Colección
        if ($request->has('coleccion') && $request->coleccion != '') {
            $query->where('coleccion_id', $request->coleccion);
        }

        // Ejecutamos la consulta final con los filtros aplicados
        $productos = $query->get();

        // Recuperamos las colecciones y categorías para poblar dinámicamente los selectores del Sidebar
        $categorias = Categoria::all();
        $colecciones = Coleccion::all();

        // Retornamos la vista enviando las colecciones y categorías dinámicas
        return view('frontend.productos', compact('productos', 'categorias', 'colecciones'));


          // --- FILTROS DE VARIANTES (Talle y Color) ---
        // Filtro por Talle
        if ($request->has('talle') && $request->talle != '') {
        $query->where('talle', $request->talle);
    }

        // Filtro por Color (Asumiendo que mapeas por relación o por el string del campo)
        if ($request->has('color') && $request->color != '') {
        // Si tu tabla guarda el nombre formateado o usas slugs
        $query->whereHas('color', function($q) use ($request) {
            $q->where('nombre', 'LIKE', '%' . $request->color . '%');
    });
}
    }

  

    /**
     * Muestra el listado de productos en el panel de administración.
     */
    public function adminIndex()
    {
        $productos = Producto::with('categoria')->get();
        return view('backend.admin.productos', compact('productos'));
    }

    /**
     * Formulario de creación de productos (Panel de Administración).
     */
    public function create()
    {
        $categorias = Categoria::all();
        $colecciones = Coleccion::all();
        $colores = Color::all();

        return view('backend.admin.productos', compact('categorias', 'colecciones', 'colores'));
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
            'coleccion_id'  => 'nullable|exists:colecciones,id',
            'color_id'      => 'required|exists:colores,id',
            'tipo_mascota'  => 'required|in:perro,gato,ambos',
            'precio'        => 'required|numeric|min:0',
            
            // Validamos que envíe al menos un talle con su stock
            'variantes'     => 'required|array|min:1',
            'variantes.*.talle' => 'required|string|max:10',
            'variantes.*.stock' => 'required|integer|min:0',
            
            // Validamos las imágenes
            'imagenes'      => 'nullable|array',
            'imagenes.*'    => 'url' // O 'image|mimes:jpeg,png' si hacen uploads físicos
        ]);

        // Iniciamos una transacción de Base de Datos para asegurar que no queden datos huérfanos si algo falla
        DB::beginTransaction();

        try {
            $skuBase = strtoupper($request->sku_base);
            $colorNom = Color::find($request->color_id)->nombre;
            $skuColor = $skuBase . '-' . strtoupper($colorNom);

            // 1. Guardar cada variante de talle como una fila única en 'productos'
            foreach ($request->variantes as $variante) {
                
                // Obtenemos el talle directamente de la variante (string)
                $talleNom = $variante['talle'];
                $skuVariante = $skuColor . '-' . strtoupper($talleNom);

                Producto::create([
                    'categoria_id' => $request->categoria_id,
                    'coleccion_id' => $request->coleccion_id,
                    'color_id'     => $request->color_id,
                    'talle'        => $talleNom,
                    'nombre'       => $request->nombre,
                    'descripcion'  => $request->descripcion,
                    'tipo_mascota' => $request->tipo_mascota,
                    'sku_base'     => $skuBase,
                    'sku_color'    => $skuColor,
                    'sku'          => $skuVariante,
                    'stock'        => $variante['stock'],
                    'stock_minimo' => $request->stock_minimo ?? 2,
                    'precio'       => $request->precio,
                ]);
            }

            // 2. Guardar las URLs de las imágenes asociándolas al grupo común (sku_color)
            if ($request->has('imagenes')) {
                foreach ($request->imagenes as $index => $url) {
                    ProductoImagen::create([
                        'sku_color' => $skuColor,
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
        $productoBase = Producto::where('sku_base', $sku_base)->with(['categoria', 'color'])->firstOrFail();

        // Traemos todas las variantes de talle que tengan stock disponible
        $variantesDisponibles = Producto::where('sku_base', $sku_base)
            ->where('stock', '>', 0)
            ->get();

        // Traemos el carrusel de imágenes asociadas al color de la variante base
        $imagenes = ProductoImagen::where('sku_color', $productoBase->sku_color)->orderBy('orden', 'asc')->get();

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