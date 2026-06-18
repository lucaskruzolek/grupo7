<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ColeccionController extends Controller
{
    /**
     * Muestra el listado de todas las colecciones.
     */
    public function index()
    {
        $colecciones = Coleccion::withCount(['productos' => function ($query) {
            $query->select(\Illuminate\Support\Facades\DB::raw('count(distinct(sku_base))'));
        }])->get();
        return view('backend.admin.colecciones', compact('colecciones'));
    }

    /**
     * Formulario para crear (Redirige al index administrado por Modal).
     */
    public function create()
    {
        return redirect()->route('admin.colecciones.index');
    }

    /**
     * Guarda la colección en la base de datos con soporte opcional para subida de archivo a R2 o URL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'url_imagen'   => 'nullable|url',
            'imagen_file'  => 'nullable|image|max:5120', // Max 5MB
        ]);

        $urlImagen = $request->url_imagen;

        // Si se cargó un archivo local
        if ($request->hasFile('imagen_file')) {
            $file = $request->file('imagen_file');
            
            // Generar nombre de archivo único con la extensión original
            $fileName = 'colecciones/' . Str::slug($request->nombre) . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Subir a R2 (disco s3)
            $path = Storage::disk('s3')->putFileAs('', $file, $fileName, 'public');
            
            if ($path) {
                $urlImagen = Storage::disk('s3')->url($fileName);
            }
        }

        Coleccion::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'url_imagen'  => $urlImagen,
        ]);

        return redirect()->route('admin.colecciones.index')->with('exito', 'Colección creada con éxito.');
    }

    /**
     * Formulario de edición (Redirige al index administrado por Modal).
     */
    public function edit(Coleccion $coleccion)
    {
        return redirect()->route('admin.colecciones.index');
    }

    /**
     * Actualiza la colección seleccionada.
     */
    public function update(Request $request, Coleccion $coleccion)
    {
        $request->validate([
            'nombre'       => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'url_imagen'   => 'nullable|url',
            'imagen_file'  => 'nullable|image|max:5120',
        ]);

        // Por defecto conservamos la imagen actual si existe
        $urlImagen = $coleccion->url_imagen;

        // Si se subió un nuevo archivo
        if ($request->hasFile('imagen_file')) {
            $file = $request->file('imagen_file');
            
            // Eliminar imagen anterior de R2 si existía y era local
            if ($coleccion->url_imagen) {
                $urlPath = parse_url($coleccion->url_imagen, PHP_URL_PATH);
                $relativeStoragePath = ltrim($urlPath, '/');
                if (str_starts_with($relativeStoragePath, 'storage/')) {
                    $relativeStoragePath = substr($relativeStoragePath, 8);
                }
                
                if (Storage::disk('s3')->exists($relativeStoragePath)) {
                    Storage::disk('s3')->delete($relativeStoragePath);
                }
            }

            // Subir nueva imagen
            $fileName = 'colecciones/' . Str::slug($request->nombre) . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('s3')->putFileAs('', $file, $fileName, 'public');
            
            if ($path) {
                $urlImagen = Storage::disk('s3')->url($fileName);
            }
        } elseif ($request->has('url_imagen')) {
            // Si no se cargó un archivo, pero el formulario envió el campo url_imagen (modo URL activo)
            $urlImagen = $request->url_imagen;
        }

        $coleccion->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'url_imagen'  => $urlImagen,
        ]);

        return redirect()->route('admin.colecciones.index')->with('exito', 'Colección actualizada con éxito.');
    }

    /**
     * Baja lógica de la colección (SoftDelete).
     */
    public function destroy(Coleccion $coleccion)
    {
        // Borrar la imagen de R2 si existía y era local
        if ($coleccion->url_imagen) {
            $urlPath = parse_url($coleccion->url_imagen, PHP_URL_PATH);
            $relativeStoragePath = ltrim($urlPath, '/');
            if (str_starts_with($relativeStoragePath, 'storage/')) {
                $relativeStoragePath = substr($relativeStoragePath, 8);
            }
            
            if (Storage::disk('s3')->exists($relativeStoragePath)) {
                Storage::disk('s3')->delete($relativeStoragePath);
            }
        }

        $coleccion->delete();
        return redirect()->route('admin.colecciones.index')->with('exito', 'Colección dada de baja correctamente.');
    }
}
