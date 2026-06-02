<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    /**
     * Lista todas las categorías con sus subcategorías hijas asociadas.
     */
    public function index()
    {
        // Trae solo las categorías raíz (padres) y precarga sus hijas para evitar el problema N+1
        $categorias = Categoria::whereNull('parent_id')->with('children')->get();
        return view('backend.admin.categorias', compact('categorias'));
    }

    /**
     * Formulario de creación (Redirige al index ya que se gestiona por Modal).
     */
    public function create()
    {
        return redirect()->route('admin.categorias.index');
    }

    /**
     * Guarda una categoría o subcategoría en la base de datos con carga opcional de SVG a R2.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categorias,id', // Si viene, debe ser un ID válido de categoría
            'icono'     => 'nullable|file|max:2048', // Valida tamaño de archivo
        ]);

        $iconoUrl = null;

        // Si es una categoría principal y se sube un icono
        if ($request->hasFile('icono') && !$request->filled('parent_id')) {
            $file = $request->file('icono');
            
            // Validación manual y segura de extensión SVG
            if (strtolower($file->getClientOriginalExtension()) !== 'svg') {
                return redirect()->back()->withInput()->with('error', 'El icono debe ser un archivo con formato SVG.');
            }

            // Generar nombre de archivo único
            $fileName = 'icons/' . Str::slug($request->nombre) . '_' . time() . '_' . uniqid() . '.svg';
            
            // Subir a R2 (disco s3)
            $path = Storage::disk('s3')->putFileAs('', $file, $fileName, 'public');
            
            if ($path) {
                $iconoUrl = Storage::disk('s3')->url($fileName);
            }
        }

        Categoria::create([
            'nombre'    => $request->nombre,
            'parent_id' => $request->parent_id ?: null,
            'icono'     => $iconoUrl,
        ]);

        return redirect()->route('admin.categorias.index')->with('exito', 'Categoría/Subcategoría guardada con éxito.');
    }

    /**
     * Muestra el formulario para editar (Redirige al index ya que se gestiona por Modal).
     */
    public function edit(Categoria $categoria)
    {
        return redirect()->route('admin.categorias.index');
    }

    /**
     * Actualiza la categoría.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categorias,id',
            'icono'     => 'nullable|file|max:2048',
        ]);

        $iconoUrl = $categoria->icono;

        // Si se subió un nuevo icono
        if ($request->hasFile('icono')) {
            $file = $request->file('icono');
            
            // Validación manual y segura de extensión SVG
            if (strtolower($file->getClientOriginalExtension()) !== 'svg') {
                return redirect()->back()->withInput()->with('error', 'El icono debe ser un archivo con formato SVG.');
            }

            // Borrar el icono anterior de R2 si existía
            if ($categoria->icono) {
                $urlPath = parse_url($categoria->icono, PHP_URL_PATH);
                $relativeStoragePath = ltrim($urlPath, '/');
                if (str_starts_with($relativeStoragePath, 'storage/')) {
                    $relativeStoragePath = substr($relativeStoragePath, 8);
                }
                
                if (Storage::disk('s3')->exists($relativeStoragePath)) {
                    Storage::disk('s3')->delete($relativeStoragePath);
                }
            }

            // Subir nuevo icono
            $fileName = 'icons/' . Str::slug($request->nombre) . '_' . time() . '_' . uniqid() . '.svg';
            $path = Storage::disk('s3')->putFileAs('', $file, $fileName, 'public');
            if ($path) {
                $iconoUrl = Storage::disk('s3')->url($fileName);
            }
        }

        // Si se convierte a subcategoría (tiene parent_id), no debe tener icono.
        if ($request->filled('parent_id')) {
            if ($categoria->icono) {
                $urlPath = parse_url($categoria->icono, PHP_URL_PATH);
                $relativeStoragePath = ltrim($urlPath, '/');
                if (str_starts_with($relativeStoragePath, 'storage/')) {
                    $relativeStoragePath = substr($relativeStoragePath, 8);
                }
                
                if (Storage::disk('s3')->exists($relativeStoragePath)) {
                    Storage::disk('s3')->delete($relativeStoragePath);
                }
            }
            $iconoUrl = null;
        }

        $categoria->update([
            'nombre'    => $request->nombre,
            'parent_id' => $request->parent_id ?: null,
            'icono'     => $iconoUrl,
        ]);

        return redirect()->route('admin.categorias.index')->with('exito', 'Categoría actualizada correctamente.');
    }

    /**
     * Eliminación lógica (SoftDelete). Restringe si hay productos asociados.
     */
    public function destroy(Categoria $categoria)
    {
        // 1. Verificar si la categoría tiene productos directamente asociados
        if ($categoria->productos()->exists()) {
            return redirect()->route('admin.categorias.index')->with('error', 'No se puede eliminar la categoría "' . $categoria->nombre . '" porque tiene productos asociados.');
        }

        // 2. Si es una categoría principal, verificar subcategorías hijas
        if ($categoria->parent_id === null) {
            foreach ($categoria->children as $child) {
                if ($child->productos()->exists()) {
                    return redirect()->route('admin.categorias.index')->with('error', 'No se puede eliminar la categoría "' . $categoria->nombre . '" porque su subcategoría "' . $child->nombre . '" tiene productos asociados.');
                }
            }

            // Si no hay productos asociados, damos de baja a las subcategorías en cascada lógica
            $categoria->children()->delete();
        }

        // Borrar el icono de R2 si existía (opcional, pero buena práctica para ahorrar espacio)
        if ($categoria->icono) {
            $urlPath = parse_url($categoria->icono, PHP_URL_PATH);
            $relativeStoragePath = ltrim($urlPath, '/');
            if (str_starts_with($relativeStoragePath, 'storage/')) {
                $relativeStoragePath = substr($relativeStoragePath, 8);
            }
            
            if (Storage::disk('s3')->exists($relativeStoragePath)) {
                Storage::disk('s3')->delete($relativeStoragePath);
            }
        }

        // 3. Dar de baja a la categoría
        $categoria->delete();
        
        return redirect()->route('admin.categorias.index')->with('exito', 'Categoría dada de baja.');
    }
}
