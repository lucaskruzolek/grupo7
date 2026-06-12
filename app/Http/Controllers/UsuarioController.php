<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Muestra el listado de clientes/usuarios en el panel administrativo.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'all');
        $startDate = null;
        $endDate = null;

        if ($period !== 'all') {
            switch ($period) {
                case 'today':
                    $startDate = \Carbon\Carbon::today()->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfDay();
                    break;
                case '7days':
                    $startDate = \Carbon\Carbon::today()->subDays(6)->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfDay();
                    break;
                case 'month':
                    $startDate = \Carbon\Carbon::today()->startOfMonth()->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfMonth()->endOfDay();
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $startDate = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
                        $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();
                    } else {
                        $startDate = \Carbon\Carbon::today()->startOfMonth()->startOfDay();
                        $endDate = \Carbon\Carbon::today()->endOfMonth()->endOfDay();
                    }
                    break;
            }
        }

        // --- CALCULAR KPIs ---
        // Total de usuarios registrados (histórico)
        $totalUsuarios = Usuario::count();

        // Nuevos registros en el período
        $queryNuevos = Usuario::query();
        if ($startDate && $endDate) {
            $queryNuevos->whereBetween('created_at', [$startDate, $endDate]);
        }
        $nuevosRegistros = $queryNuevos->count();

        // Compradores activos (usuarios con compras confirmadas/despachadas en el período)
        $queryCompradores = Usuario::whereHas('ventas', function ($q) use ($startDate, $endDate) {
            $q->whereIn('estado', ['CONFIRMADO', 'DESPACHADO']);
            if ($startDate && $endDate) {
                $q->whereBetween('fecha_venta', [$startDate, $endDate]);
            }
        });
        $compradoresActivos = $queryCompradores->count();

        // Total de administradores (histórico)
        $totalAdmins = Usuario::whereHas('rol', function ($q) {
            $q->where('nombre', 'admin');
        })->count();


        // --- LISTADO DE USUARIOS ---
        $queryUsuariosList = Usuario::with('rol')
            ->orderBy('created_at', 'desc');

        // Filtro de fecha
        if ($startDate && $endDate) {
            $queryUsuariosList->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filtro por Rol
        if ($request->filled('rol') && $request->input('rol') !== 'all') {
            $queryUsuariosList->whereHas('rol', function ($q) use ($request) {
                $q->where('nombre', $request->input('rol'));
            });
        }

        // Filtro por búsqueda (Nombre, Apellido, Email, ID)
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $searchClean = ltrim($request->input('search'), '#');
            $searchCleanLike = '%' . $searchClean . '%';

            $queryUsuariosList->where(function ($q) use ($search, $searchCleanLike) {
                $q->where('id', 'like', $searchCleanLike)
                  ->orWhere('nombre', 'like', $search)
                  ->orWhere('apellido', 'like', $search)
                  ->orWhere('email', 'like', $search);
                
                $driver = DB::connection()->getDriverName();
                if ($driver === 'sqlite') {
                    $q->orWhereRaw("nombre || ' ' || apellido LIKE ?", [$search]);
                } else {
                    $q->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", [$search]);
                }
            });
        }

        $usuarios = $queryUsuariosList->paginate(10);
        $roles = Rol::all();

        return view('backend.admin.clientes', compact(
            'usuarios',
            'roles',
            'totalUsuarios',
            'nuevosRegistros',
            'compradoresActivos',
            'totalAdmins'
        ));
    }

    /**
     * Muestra el detalle del usuario (vía AJAX para el Offcanvas).
     */
    public function show(Request $request, $id)
    {
        $usuario = Usuario::with(['rol', 'ventas' => function($q) {
            $q->whereIn('estado', ['CONFIRMADO', 'DESPACHADO'])
              ->orderBy('fecha_venta', 'desc')
              ->with('formaPago');
        }])->findOrFail($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'usuario' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'email' => $usuario->email,
                    'rol_id' => $usuario->rol_id,
                    'rol_nombre' => ucfirst($usuario->rol->nombre),
                    'created_at' => $usuario->created_at ? $usuario->created_at->format('d M Y H:i \h\s') : '-',
                    'telefono' => $usuario->telefono ?? 'No especificado',
                    'direccion' => $usuario->direccion ?? 'No especificada',
                    'localidad' => $usuario->localidad ?? 'No especificada',
                    'provincia' => $usuario->provincia ?? 'No especificada',
                    'codigo_postal' => $usuario->codigo_postal ?? 'No especificado',
                    'ventas' => $usuario->ventas->map(function ($venta) {
                        return [
                            'id' => $venta->id,
                            'fecha_venta' => $venta->fecha_venta ? $venta->fecha_venta->format('d M Y H:i \h\s') : $venta->created_at->format('d M Y H:i \h\s'),
                            'total' => $venta->total,
                            'estado' => $venta->estado,
                            'forma_pago' => $venta->formaPago ? $venta->formaPago->descripcion : 'No especificada',
                        ];
                    })
                ]
            ]);
        }

        // Si no es petición AJAX, redirige
        return redirect()->route('admin.clientes');
    }

    /**
     * Crea un nuevo usuario (admin o cliente).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'email'         => 'required|email|unique:usuarios,email',
            'password'      => 'required|min:6|confirmed', 
            'rol_id'        => 'required|exists:roles,id',
            'telefono'      => 'nullable|string|max:50',
            'direccion'     => 'nullable|string|max:255',
            'localidad'     => 'nullable|string|max:100',
            'provincia'     => 'nullable|string|max:100',
            'codigo_postal' => 'nullable|string|max:20',
        ]);

        Usuario::create([
            'nombre'        => $request->nombre,
            'apellido'      => $request->apellido,
            'email'         => $request->email,
            'password'      => Hash::make($request->password), // Hashear password
            'rol_id'        => $request->rol_id,
            'telefono'      => $request->telefono,
            'direccion'     => $request->direccion,
            'localidad'     => $request->localidad,
            'provincia'     => $request->provincia,
            'codigo_postal' => $request->codigo_postal,
        ]);

        return redirect()->route('admin.clientes')->with('exito', 'Usuario creado con éxito.');
    }

    /**
     * Elimina lógicamente un usuario (no-admin).
     */
    public function destroy(Usuario $usuario)
    {
        // Seguridad: evitar que el administrador elimine otros administradores
        if ($usuario->rol->nombre === 'admin') {
            return redirect()->route('admin.clientes')->withErrors(['No se puede eliminar un usuario administrador.']);
        }

        $usuario->delete(); // SoftDelete

        return redirect()->route('admin.clientes')->with('exito', 'Usuario eliminado con éxito.');
    }

    /**
     * Muestra el perfil y las compras del usuario autenticado (Mi Cuenta).
     */
    public function miCuenta()
    {
        $usuario = auth()->user();
        $ventas = Venta::where('usuario_id', $usuario->id)
            ->ventas()
            ->with(['formaPago', 'detalles.producto'])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        return view('frontend.mi-cuenta', compact('usuario', 'ventas'));
    }

    /**
     * Actualiza los datos de perfil del usuario autenticado.
     */
    public function actualizarDatos(Request $request)
    {
        $usuario = auth()->user();

        $request->validate([
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'email'           => 'required|email|unique:usuarios,email,' . $usuario->id,
            'telefono'        => 'nullable|string|max:50',
            'direccion'       => 'nullable|string|max:255',
            'localidad'       => 'nullable|string|max:100',
            'provincia'       => 'nullable|string|max:100',
            'codigo_postal'   => 'nullable|string|max:20',
            'password_actual' => 'nullable|required_with:password_nueva',
            'password_nueva'  => 'nullable|min:6|confirmed',
        ], [
            'password_actual.required_with' => 'Debe ingresar la contraseña actual para establecer una nueva.',
            'password_nueva.min'            => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password_nueva.confirmed'      => 'Las nuevas contraseñas no coinciden.',
            'email.unique'                  => 'El correo electrónico ya está en uso por otro usuario.',
        ]);

        // Verificar contraseña actual si se intenta cambiar la contraseña
        if ($request->filled('password_nueva')) {
            if (!Hash::check($request->password_actual, $usuario->password)) {
                return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.'])->withInput();
            }
            $usuario->password = Hash::make($request->password_nueva);
        }

        // Actualizar datos
        $usuario->fill($request->only([
            'nombre',
            'apellido',
            'email',
            'telefono',
            'direccion',
            'localidad',
            'provincia',
            'codigo_postal'
        ]));

        $usuario->save();

        return redirect()->route('usuario.cuenta')->with('exito', 'Tus datos se actualizaron con éxito.');
    }
}
