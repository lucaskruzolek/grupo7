<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function formularioLogin()
    {
        return view('backend.usuarios.login');
    }

    /**
     * Muestra el formulario de registro.
     */
    public function formularioRegistro()
    {
        return view('backend.usuarios.register');
    }

    /**
     * Procesa el formulario de registro: valida, crea el usuario y lo loguea.
     */
    public function registrar(Request $request)
    {
        // Validación con nombres de campos correctos (coinciden con la migración)
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Obtener el rol 'cliente' (se crea automáticamente si no existe)
        $rolCliente = Rol::firstOrCreate(
            ['nombre' => 'cliente'],
            ['descripcion' => 'Usuario cliente registrado']
        );

        // Crear el usuario — el cast 'hashed' del modelo hashea el password automáticamente
        $usuario = Usuario::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'password' => $request->password,
            'rol_id'   => $rolCliente->id,
        ]);

        // Loguear al usuario recién creado y regenerar sesión
        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('inicio');
    }

    /**
     * Procesa el login: valida credenciales, inicia sesión y redirige según rol.
     */
    public function autenticar(Request $request)
    {
        $credenciales = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        // Auth::attempt() busca en la tabla 'usuarios' (configurado en auth.php)
        if (Auth::attempt($credenciales, $remember)) {
            $request->session()->regenerate();

            // Acceso correcto al rol a través de la relación Eloquent
            if (Auth::user()->rol->nombre === 'admin') {
                return redirect('/admin');
            }

            return redirect()->route('inicio');
        }

        // Si las credenciales son incorrectas, vuelve al login con error
        return back()->withErrors([
            'email' => 'Email o contraseña incorrectos.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario autenticado.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
