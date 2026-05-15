<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Rol::all();                  // SoftDelete filtra deleted_at automáticamente
            return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:50|unique:roles',
            'descripcion' => 'nullable|string|max:255',
        ]);
        Rol::create($request->only(['nombre', 'descripcion'])); // usa $fillable del Model
        return redirect()->route('roles.index')->with('exito', 'Rol creado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rol $rol)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rol $rol)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rol $rol)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rol $rol)
    {
        $rol->delete();                       // SoftDelete: setea deleted_at, no borra la fila
        return redirect()->route('roles.index')->with('exito', 'Rol eliminado.');
    }
}
