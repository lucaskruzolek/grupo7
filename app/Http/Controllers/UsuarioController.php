<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // <-- IMPORTANTE

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|min:8|confirmed', 
            'rol_id'   => 'required|exists:roles,id',
        ]);

        // Creamos el usuario asegurando la encriptación de la contraseña
        Usuario::create([
            'nombre'   => $request->nombre,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // <-- ENCRIPTACIÓN ACTIVA
            'rol_id'   => $request->rol_id,
        ]);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario registrado con éxito.');
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete(); // Borrado lógico (SoftDelete)
        return redirect()->route('usuarios.index')->with('exito', 'Usuario dado de baja.');
    }
}
