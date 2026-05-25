<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Lista todas las categorías con sus subcategorías hijas asociadas.
     */
    public function index()
    {
        // Trae solo las categorías raíz (padres) y precarga sus hijas para evitar el problema N+1
        $categorias = Categoria::whereNull('parent_id')->with('children')->get();
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Formulario de creación (Panel de Administración).
     */
    public function create()
    {
        // Traemos las categorías raíz por si el administrador quiere crear una subcategoría asignándole un padre
        $categoriasPadre = Categoria::whereNull('parent_id')->get();
        return view('categorias.create', compact('categoriasPadre'));
    }

    /**
     * Guarda una categoría o subcategoría en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categorias,id', // Si viene, debe ser un ID válido de categoría
        ]);

        Categoria::create([
            'nombre'    => $request->nombre,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('categorias.index')->with('exito', 'Categoría/Subcategoría guardada con éxito.');
    }

    /**
     * Muestra el formulario para editar una categoría.
     */
    public function edit(Categoria $categoria)
    {
        $categoriasPadre = Categoria::whereNull('parent_id')->where('id', '!=', $categoria->id)->get();
        return view('categorias.edit', compact('categoria', 'categoriasPadre'));
    }

    /**
     * Actualiza la categoría.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categorias,id',
        ]);

        $categoria->update([
            'nombre'    => $request->nombre,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('categorias.index')->with('exito', 'Categoría actualizada correctamente.');
    }

    /**
     * Eliminación lógica (SoftDelete).
     */
    public function destroy(Categoria $categoria)
    {
        // Al tener SoftDeletes, no rompe registros históricos inmediatamente en la base de datos
        $categoria->delete();
        return redirect()->route('categorias.index')->with('exito', 'Categoría dada de baja.');
    }
}
