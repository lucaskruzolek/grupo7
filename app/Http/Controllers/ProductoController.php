<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Coleccion;
use App\Models\Color;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageOptimizerService;

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

        // --- FILTROS DE VARIANTES (Talle y Color) ---
        // Filtro por Talle
        if ($request->has('talle') && $request->talle != '') {
            $query->where('talle', $request->talle);
        }

        // --- FILTRO DE COLOR POR ID REAL ---
        if ($request->has('color') && $request->color != '') {
        // Al mandar el ID, buscamos directamente en la columna 'color_id' de la tabla productos
            $query->where('color_id', $request->color);
}

        // --- FILTRO DE PRECIOS POR RANGOS PREDEFINIDOS ---
        if ($request->has('precio_rango') && $request->precio_rango != '') {
            // Usamos 'explode' para separar el string por el guion (ej: '5000-12000' se convierte en [5000, 12000])
            $partesPrecio = explode('-', $request->precio_rango);
            
            // Validamos que efectivamente tengamos las dos partes (mínimo y máximo)
            if (count($partesPrecio) === 2) {
                $precioMin = (float) $partesPrecio[0];
                $precioMax = (float) $partesPrecio[1];
                
                // Filtramos los productos cuyo precio esté dentro de ese rango
                $query->whereBetween('precio', [$precioMin, $precioMax]);
            }
        }

        // Ejecutamos la consulta final con los filtros aplicados
        $productos = $query->get();

        // Recuperamos las colecciones y categorías para poblar dinámicamente los selectores del Sidebar
        $categorias = Categoria::all();
        $colecciones = Coleccion::all();
        $colores = Color::all();

        // Retornamos la vista enviando las colecciones y categorías dinámicas
        return view('frontend.productos', compact('productos', 'categorias', 'colecciones', 'colores'));
    }

  

    /**
     * Muestra el listado de productos en el panel de administración.   
    */
    public function adminIndex()
    {
        $allProducts = Producto::select('id', 'sku_base', 'sku_color', 'nombre', 'tipo_mascota', 'categoria_id', 'color_id', 'talle', 'activo', 'coleccion_id')
            ->with([
                'categoria:id,nombre,parent_id',
                'categoria.parent:id,nombre',
                'imagenes:id,sku_color,url,orden',
                'coleccion:id,nombre'
            ])
            ->get();
        
        $grouped = $allProducts->groupBy('sku_base');
        
        $productosData = [];
        foreach ($grouped as $skuBase => $variants) {
            $first = $variants->first();
            $activeColorsCount = $variants->pluck('color_id')->unique()->filter()->count();
            $activeTallesCount = $variants->pluck('talle')->unique()->filter()->count();
            
            $firstWithImage = $variants->first(function($v) {
                return $v->imagenes->isNotEmpty();
            });
            $thumbSrc = null;
            if ($firstWithImage) {
                $sortedImgs = $firstWithImage->imagenes->sortBy('orden');
                $thumbSrc = $sortedImgs->first()->url;
            }

            $productosData[] = [
                'sku_base' => $skuBase,
                'nombre_base' => $first->nombre_base,
                'tipo_mascota' => $first->tipo_mascota,
                'categoria_id' => $first->categoria_id,
                'categoria_nombre' => $first->categoria ? $first->categoria->nombre : '',
                'categoria_padre_id' => $first->categoria ? $first->categoria->parent_id : null,
                'categoria_padre' => ($first->categoria && $first->categoria->parent) ? $first->categoria->parent->nombre : '',
                'colores_count' => $activeColorsCount,
                'talles_count' => $activeTallesCount,
                'thumb' => $thumbSrc,
                'activo' => (bool)$first->activo,
                'coleccion_id' => $first->coleccion_id,
                'coleccion_nombre' => $first->coleccion ? $first->coleccion->nombre : 'Sin colección',
            ];
        }
        
        // Ordenar alfabéticamente por nombre_base
        usort($productosData, function ($a, $b) {
            return strcasecmp($a['nombre_base'], $b['nombre_base']);
        });
        
        $categorias = Categoria::with('children')->whereNull('parent_id')->get();
        $categoriasSystem = Categoria::all()->map(function($c) {
            return [
                'id' => $c->id,
                'parent_id' => $c->parent_id,
                'pide_talle' => $c->pide_talle,
                'pide_color' => $c->pide_color
            ];
        });
        $colecciones = Coleccion::all();
        $coloresSystem = Color::all()->map(function($c) {
            return [
                'id' => $c->id,
                'name' => $c->nombre,
                'hex' => $c->hex_code,
                'key' => strtolower($c->nombre),
                'sku' => substr(strtoupper($c->nombre), 0, 3)
            ];
        });
        
        $tallesSystem = \App\Models\Producto::getTallesSystem();

        return view('backend.admin.productos', compact(
            'productosData', 
            'categorias', 
            'categoriasSystem',
            'colecciones', 
            'coloresSystem', 
            'tallesSystem'
        ));
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
     * Almacena un producto y sus variantes iniciales en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:150',
            'sku_base'      => 'required|string|max:50|unique:productos,sku_base',
            'categoria_id'  => 'required|exists:categorias,id',
            'coleccion_id'  => 'nullable|exists:colecciones,id',
            'tipo_mascota'  => 'required|in:perro,gato,ambos',
            'precio'        => 'required|numeric|min:0',
            'stock_minimo'  => 'required|integer|min:0',
            'variantes'     => 'required|array|min:1',
            'variantes.*.color_id' => 'required|exists:colores,id',
            'variantes.*.talle'    => 'required|string|max:10',
            'variantes.*.stock'    => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $skuBase = strtoupper($request->sku_base);
            $stockMinimo = (int)$request->stock_minimo;

            foreach ($request->variantes as $vData) {
                $colorObj = Color::findOrFail($vData['color_id']);
                $colorNom = $colorObj->nombre;
                $skuColor = $skuBase . '-' . strtoupper($colorNom);
                $talle = strtoupper($vData['talle']);
                $skuVariante = $skuColor . '-' . $talle;

                Producto::create([
                    'categoria_id' => $request->categoria_id,
                    'coleccion_id' => $request->coleccion_id,
                    'color_id'     => $vData['color_id'],
                    'talle'        => $talle,
                    'nombre'       => $request->nombre . ' - ' . $colorNom . ($talle === '-' ? '' : ' ' . $talle),
                    'descripcion'  => $request->descripcion ?? '',
                    'tipo_mascota' => $request->tipo_mascota,
                    'sku_base'     => $skuBase,
                    'sku_color'    => $skuColor,
                    'sku'          => $skuVariante,
                    'stock'        => $vData['stock'],
                    'stock_minimo' => $stockMinimo,
                    'precio'       => $request->precio,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.productos.index')->with('exito', 'Producto y sus variantes creados con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza la información comercial básica y las variantes de stock de un grupo de productos.
     */
    public function updateGroup(Request $request)
    {
        $request->validate([
            'sku_base'     => 'required|string',
            'nombre_base'  => 'required|string|max:150',
            'descripcion'  => 'nullable|string',
            'tipo_mascota' => 'required|in:perro,gato,ambos',
            'precio'       => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'variantes'    => 'required|array',
            'variantes.*.favorito' => 'nullable|boolean',
            'coleccion_id' => 'nullable|exists:colecciones,id',
            'activo'       => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $skuBase = strtoupper($request->sku_base);
            $nombreBase = $request->nombre_base;
            $descripcion = $request->descripcion;
            $tipoMascota = $request->tipo_mascota;
            $precio = $request->precio;
            $stockMinimo = (int)$request->stock_minimo;
            $coleccionId = $request->coleccion_id;
            $activo = (bool)$request->activo;

            // Obtener variantes existentes del SKU Base (incluyendo borrados lógicos por si se requiere restaurar)
            $existingVariants = Producto::withTrashed()
                ->where('sku_base', $skuBase)
                ->get()
                ->keyBy(function ($v) {
                    return $v->color_id . '_' . $v->talle;
                });

            foreach ($request->variantes as $vData) {
                $colorId = $vData['color_id'];
                $talle = strtoupper($vData['talle']);
                $stock = (int)$vData['stock'];
                $favorito = isset($vData['favorito']) ? (bool)$vData['favorito'] : false;

                $colorObj = Color::find($colorId);
                if (!$colorObj) continue;

                $colorNom = $colorObj->nombre;
                $skuColor = $skuBase . '-' . strtoupper($colorNom);
                
                $key = $colorId . '_' . $talle;

                if ($existingVariants->has($key)) {
                    $variant = $existingVariants->get($key);
                    
                    if ($variant->trashed()) {
                        $variant->restore();
                    }

                    $variantName = $nombreBase . ' - ' . $colorNom . ($talle === '-' ? '' : ' ' . $talle);
                    
                    // Actualización inteligente: solo ejecutar UPDATE SQL si los valores cambiaron
                    $updates = [];
                    if ($variant->nombre !== $variantName) $updates['nombre'] = $variantName;
                    if ($variant->descripcion !== $descripcion) $updates['descripcion'] = $descripcion;
                    if ($variant->tipo_mascota !== $tipoMascota) $updates['tipo_mascota'] = $tipoMascota;
                    if ((float)$variant->precio !== (float)$precio) $updates['precio'] = $precio;
                    if ($variant->stock !== $stock) $updates['stock'] = $stock;
                    if ($variant->stock_minimo !== $stockMinimo) $updates['stock_minimo'] = $stockMinimo;
                    if ($variant->coleccion_id !== $coleccionId) $updates['coleccion_id'] = $coleccionId;
                    if ($variant->activo !== $activo) $updates['activo'] = $activo;
                    if ($variant->favorito !== $favorito) $updates['favorito'] = $favorito;

                    if (!empty($updates)) {
                        $variant->update($updates);
                    }
                } else {
                    // Crear nueva combinación variante
                    $skuVariante = $skuColor . '-' . $talle;
                    
                    // Tomar datos de categoría y colección del grupo existente
                    $rep = $existingVariants->first();
                    $categoriaId = $rep ? $rep->categoria_id : Categoria::whereNotNull('parent_id')->first()->id;

                    Producto::create([
                        'categoria_id' => $categoriaId,
                        'coleccion_id' => $coleccionId,
                        'color_id'     => $colorId,
                        'talle'        => $talle,
                        'nombre'       => $nombreBase . ' - ' . $colorNom . ($talle === '-' ? '' : ' ' . $talle),
                        'descripcion'  => $descripcion,
                        'tipo_mascota' => $tipoMascota,
                        'sku_base'     => $skuBase,
                        'sku_color'    => $skuColor,
                        'sku'          => $skuVariante,
                        'stock'        => $stock,
                        'stock_minimo' => $stockMinimo,
                        'precio'       => $precio,
                        'activo'       => $activo,
                        'favorito'     => $favorito,
                    ]);
                }
            }

            // Obtener llaves compuestas de variantes enviadas en el request
            $incomingKeys = collect($request->variantes)->map(function ($v) {
                return $v['color_id'] . '_' . strtoupper($v['talle']);
            })->toArray();

            // Soft-deletear variantes en base de datos que no vinieron en la petición
            foreach ($existingVariants as $key => $v) {
                if (!in_array($key, $incomingKeys) && !$v->trashed()) {
                    $v->delete();
                }
            }

            // Asegurar que cualquier otra variante existente en BD que no vino en el request (pero que existe) tenga los campos descriptivos sincronizados
            $allBaseVariants = Producto::where('sku_base', $skuBase)->get();
            foreach ($allBaseVariants as $v) {
                $colorNom = $v->color ? $v->color->nombre : '';
                $variantName = $nombreBase . ' - ' . $colorNom . ($v->talle === '-' ? '' : ' ' . $v->talle);

                $updates = [];
                if ($v->nombre !== $variantName) $updates['nombre'] = $variantName;
                if ($v->descripcion !== $descripcion) $updates['descripcion'] = $descripcion;
                if ($v->tipo_mascota !== $tipoMascota) $updates['tipo_mascota'] = $tipoMascota;
                if ((float)$v->precio !== (float)$precio) $updates['precio'] = $precio;
                if ($v->stock_minimo !== $stockMinimo) $updates['stock_minimo'] = $stockMinimo;
                if ($v->coleccion_id !== $coleccionId) $updates['coleccion_id'] = $coleccionId;
                if ($v->activo !== $activo) $updates['activo'] = $activo;

                if (!empty($updates)) {
                    $v->update($updates);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Producto y sus variantes actualizados correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al guardar cambios: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Devuelve los detalles de un producto específico en formato JSON (Carga bajo demanda).
     */
    public function getDetails($sku_base)
    {
        $variants = Producto::where('sku_base', $sku_base)
            ->with(['categoria.parent', 'color', 'imagenes', 'coleccion'])
            ->get();

        if ($variants->isEmpty()) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        $first = $variants->first();

        // Obtener colores activos únicos
        $activeColors = $variants->pluck('color')->unique('id')->filter()->map(function($c) use ($variants) {
            $isFav = $variants->where('color_id', $c->id)->contains('favorito', true);
            return [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'hex_code' => $c->hex_code,
                'key' => strtolower($c->nombre),
                'favorito' => (bool)$isFav,
            ];
        })->values()->toArray();

        // Obtener talles activos únicos
        $activeTalles = $variants->pluck('talle')->unique()->values()->toArray();

        // Mapear variante color/talle a stock/sku/id
        $variantsMap = [];
        foreach ($variants as $v) {
            if ($v->color) {
                $colorKey = strtolower($v->color->nombre);
                $variantsMap[$colorKey][$v->talle] = [
                    'sku' => $v->sku,
                    'stock' => $v->stock,
                    'id' => $v->id
                ];
            }
        }

        // Mapear imágenes por color
        $colorMedia = [];
        foreach ($variants as $v) {
            if ($v->color) {
                $colorKey = strtolower($v->color->nombre);
                if (!isset($colorMedia[$colorKey])) {
                    $sortedImgs = $v->imagenes->sortBy('orden');
                    $imgsData = [];
                    foreach ($sortedImgs as $img) {
                        $imgsData[] = [
                            'id' => $img->id,
                            'url' => $img->url,
                            'orden' => $img->orden
                        ];
                    }
                    $imgsUrls = $sortedImgs->pluck('url')->toArray();
                    $colorMedia[$colorKey] = [
                        'main' => count($imgsUrls) > 0 ? $imgsUrls[0] : asset('img/ui/productos/perro-buzo-verde.webp'),
                        'thumbs' => count($imgsUrls) > 0 ? $imgsUrls : [asset('img/ui/productos/perro-buzo-verde.webp')],
                        'urls' => count($imgsUrls) > 0 ? $imgsUrls : [asset('img/ui/productos/perro-buzo-verde.webp')],
                        'images' => $imgsData
                    ];
                }
            }
        }

        $productData = [
            'sku_base' => $sku_base,
            'nombre_base' => $first->nombre_base,
            'descripcion' => $first->descripcion,
            'tipo_mascota' => $first->tipo_mascota,
            'precio' => (float)$first->precio,
            'stock_minimo' => $first->stock_minimo !== null ? (int)$first->stock_minimo : null,
            'categoria_id' => $first->categoria_id,
            'categoria_nombre' => $first->categoria ? $first->categoria->nombre : '',
            'categoria_padre' => ($first->categoria && $first->categoria->parent) ? $first->categoria->parent->nombre : '',
            'coleccion_id' => $first->coleccion_id,
            'coleccion_nombre' => $first->coleccion ? $first->coleccion->nombre : 'Sin colección',
            'activo' => (bool)$first->activo,
            'created_at' => $first->created_at ? $first->created_at->format('d M, Y') : '',
            'updated_at' => $first->updated_at ? $first->updated_at->format('d M, Y') : '',
            'colores_count' => count($activeColors),
            'talles_count' => count($activeTalles),
            'colores' => $activeColors,
            'talles' => $activeTalles,
            'variantes' => $variantsMap,
            'colorMedia' => $colorMedia,
        ];

        return response()->json($productData);
    }

    /**
     * Sube y procesa una imagen para guardarla en Cloudflare R2 y registrarla.
     */
    public function uploadImage(Request $request, ImageOptimizerService $imageOptimizer)
    {
        $request->validate([
            'sku_color' => 'required|string',
            'image' => 'required|image|max:5120', // Máx 5MB
        ]);

        try {
            $skuColor = $request->input('sku_color');
            $file = $request->file('image');

            // Optimizar y convertir a WebP usando el servicio de GD
            $tempPath = $imageOptimizer->convertToWebp($file, 1200, 80);

            // Calcular el orden secuencial siguiente
            $nextOrden = ProductoImagen::where('sku_color', $skuColor)->max('orden') ?? 0;
            $nextOrden++;

            // Generar un nombre de archivo predecible (ej: buzo-polar-rojo-1.webp)
            $fileName = '/img/productos/' . strtolower($skuColor) . '-' . $nextOrden . '.webp';

            // Subir a Cloudflare R2 (mediante el disco s3)
            $path = Storage::disk('s3')->putFileAs('', new \Illuminate\Http\File($tempPath), $fileName, 'public');

            // Borrar el archivo temporal
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            if (!$path) {
                return response()->json(['success' => false, 'message' => 'No se pudo subir la imagen al almacenamiento.'], 500);
            }

            // Obtener la URL pública del archivo
            $url = Storage::disk('s3')->url($fileName);

            // Crear el registro en la base de datos
            $productoImagen = ProductoImagen::create([
                'sku_color' => $skuColor,
                'url' => $url,
                'orden' => $nextOrden,
            ]);

            return response()->json([
                'success' => true,
                'image' => [
                    'id' => $productoImagen->id,
                    'url' => $productoImagen->url,
                    'orden' => $productoImagen->orden,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina una imagen del almacenamiento físico y de la base de datos.
     */
    public function deleteImage($id)
    {
        try {
            $img = ProductoImagen::findOrFail($id);
            $skuColor = $img->sku_color;

            // Extraer la ruta relativa de la URL para borrar de R2
            $urlPath = parse_url($img->url, PHP_URL_PATH);
            $relativeStoragePath = ltrim($urlPath, '/');

            // Si la URL contiene un prefijo 'storage/', lo removemos (común en testing/fakes)
            if (str_starts_with($relativeStoragePath, 'storage/')) {
                $relativeStoragePath = substr($relativeStoragePath, 8);
            }

            // Eliminar de R2
            if (Storage::disk('s3')->exists($relativeStoragePath)) {
                Storage::disk('s3')->delete($relativeStoragePath);
            }

            // Eliminar de base de datos
            $img->delete();

            // Reordenar las imágenes restantes de este sku_color
            $remaining = ProductoImagen::where('sku_color', $skuColor)->orderBy('orden', 'asc')->get();
            foreach ($remaining as $index => $item) {
                $item->update(['orden' => $index + 1]);
            }

            return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Define una imagen como portada (orden = 1) y reordena el resto.
     */
    public function setCoverImage($id)
    {
        try {
            $coverImg = ProductoImagen::findOrFail($id);
            $skuColor = $coverImg->sku_color;

            DB::beginTransaction();

            // Poner todas las imágenes del sku_color temporalmente con un orden alto o reordenarlas
            $allImgs = ProductoImagen::where('sku_color', $skuColor)->get();
            
            // Asignar orden = 1 a la seleccionada
            $coverImg->update(['orden' => 1]);

            // Reordenar las demás imágenes a partir de orden = 2
            $order = 2;
            foreach ($allImgs as $img) {
                if ($img->id !== $coverImg->id) {
                    $img->update(['orden' => $order]);
                    $order++;
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Imagen de portada establecida correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
        // El softdeletes se encargará de ocultar todas las variantes del SKU Base
        Producto::where('sku_base', $sku_base)->delete();

        return redirect()->route('admin.productos.index')->with('exito', 'El catálogo de este artículo fue dado de baja.');
    }
}