<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;
use Illuminate\Http\Request;

class ColeccionController extends Controller
{
    /**
     * Muestra el listado de todas las colecciones.
     */
    public function index()
    {
        $colecciones = Coleccion::all();
        return view('colecciones.index', compact('colecciones'));
    }

    /**
     * Formulario para crear una nueva colección.
     */
    public function create()
    {
        return view('colecciones.create');
    }

    /**
     * Guarda la colección en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'url_imagen'  => 'nullable|url', // Valida que sea un enlace correcto para el banner
        ]);

        Coleccion::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'url_imagen'  => $request->url_imagen,
        ]);

        return redirect()->route('colecciones.index')->with('exito', 'Colección creada con éxito.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Coleccion $coleccion)
    {
        return view('colecciones.edit', compact('coleccion'));
    }

    /**
     * Actualiza la colección seleccionada.
     */
    public function update(Request $request, Coleccion $coleccion)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'url_imagen'  => 'nullable|url',
        ]);

        $coleccion->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'url_imagen'  => $request->url_imagen,
        ]);

        return redirect()->route('colecciones.index')->with('exito', 'Colección actualizada con éxito.');
    }

    /**
     * Baja lógica de la colección (SoftDelete).
     */
    public function destroy(Coleccion $coleccion)
    {
        $coleccion->delete();
        return redirect()->route('colecciones.index')->with('exito', 'Colección dada de baja correctamente.');
    }
}
