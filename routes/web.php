<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController; 
use App\Http\Controllers\ColeccionController; 
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\VentaController; 
use App\Http\Controllers\ConsultaController;
use App\Models\Categoria;
use App\Models\Coleccion;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 1. PÁGINAS INFORMATIVAS (FRONTEND STÁTICO Y HOME)
// La raíz de tu sitio ahora carga correctamente el inicio de Petthreads sin ser pisada
Route::get('/', function () {
    $coleccionInvierno = Coleccion::where('nombre', 'Invierno')->first();
    $coleccionNuevos = Coleccion::where('nombre', 'Nuevos')->first();
    $coleccionPicnic = Coleccion::where('nombre', 'Picnic')->first();
    
    $categoriaRopa = Categoria::where('nombre', 'ropa')->first();
    $categoriaAccesorios = Categoria::where('nombre', 'accesorios')->first();
    $categoriaArneces = Categoria::where('nombre', 'arneces')->first();
    
    return view('frontend.principal', compact(
        'coleccionInvierno',
        'coleccionNuevos',
        'coleccionPicnic',
        'categoriaRopa',
        'categoriaAccesorios',
        'categoriaArneces'
    ));
})->name('inicio');

Route::get('/contacto', function () {
    return view('frontend.contacto');
});

Route::get('/comercializacion', function () {
    return view('frontend.comercializacion');
});

Route::get('/quienes-somos', function () {
    return view('frontend.quienes-somos');
});

Route::get('/consultas', function () {
    return view('frontend.consultas');
});

Route::post('/consultas', [ConsultaController::class, 'store'])->name('consultas.store');

Route::get('/terminos', function () {
    return view('frontend.terminos-de-uso');
});


// 2. AUTENTICACIÓN (LOGIN, REGISTRO Y LOGOUT)
// Rutas accesibles solo para visitantes NO autenticados (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'formularioLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'autenticar'])->name('login.autenticar');
    Route::get('/register', [AuthController::class, 'formularioRegistro'])->name('register');
    Route::post('/register', [AuthController::class, 'registrar'])->name('register.registrar');
});

// Logout requiere estar autenticado
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// 3. CARRITO Y CHECKOUT — CLIENTE AUTENTICADO
Route::middleware('auth')->group(function () {
    // Rutas del carrito protegidas para que el admin no pueda acceder (sólo clientes)
    Route::middleware('rol:cliente')->group(function () {
        Route::get('/carrito', [VentaController::class, 'verCarrito'])->name('carrito.ver');
        Route::post('/carrito/agregar', [VentaController::class, 'agregarAlCarrito'])->name('carrito.agregar');
        Route::patch('/carrito/detalle/{id}', [VentaController::class, 'actualizarCantidad'])->name('carrito.actualizar');
        Route::delete('/carrito/detalle/{id}', [VentaController::class, 'eliminarDelCarrito'])->name('carrito.eliminar');
        Route::post('/carrito/checkout', [VentaController::class, 'checkout'])->name('carrito.checkout');
        
        // Vista de éxito de la compra
        Route::get('/compra/exito/{id}', [VentaController::class, 'compraExito'])->name('compra.exito');
        
        // Descarga de factura por parte del cliente
        Route::get('/compra/{id}/factura', [VentaController::class, 'descargarFacturaCliente'])->name('compras.factura');
    });
    
    // Perfil y compras del usuario (Mi Cuenta)
    Route::get('/mi-cuenta', [UsuarioController::class, 'miCuenta'])->name('usuario.cuenta');
    Route::put('/mi-cuenta/actualizar', [UsuarioController::class, 'actualizarDatos'])->name('usuario.actualizar');
});


// 3. MOSTRADOR DE PRODUCTOS DINÁMICO (CON FILTROS) — PÚBLICO
// Carga el controlador para procesar los filtros del sidebar, NO una vista estática
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

// Esta es la ruta encargada de mostrar el detalle de la prenda mediante su código de modelo
Route::get('productos/{sku_base}', [ProductoController::class, 'show'])->name('productos.show');


// 4. MANTENIMIENTO Y CRUD DE ADMINISTRACIÓN (BACKEND) — PROTEGIDO
// Solo usuarios autenticados con rol 'admin' pueden acceder
Route::middleware(['auth', 'rol:admin'])->prefix('admin')->group(function () {
    // Panel principal y páginas estáticas de administración
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard/chart-data', [AdminController::class, 'chartData'])->name('admin.dashboard.chart-data');
    Route::get('/consultas', [ConsultaController::class, 'index'])->name('admin.consultas');
    Route::post('/consultas/{id}/toggle-leido', [ConsultaController::class, 'toggleLeido'])->name('admin.consultas.toggle-leido');
    Route::post('/consultas/{id}/toggle-respondido', [ConsultaController::class, 'toggleRespondido'])->name('admin.consultas.toggle-respondido');
    Route::delete('/consultas/{id}', [ConsultaController::class, 'destroy'])->name('admin.consultas.destroy');
    Route::get('/clientes', [UsuarioController::class, 'index'])->name('admin.clientes');

    // Listado de productos administrativo (separado del catálogo público)
    Route::get('/productos', [ProductoController::class, 'adminIndex'])->name('admin.productos.index');

    // Recursos CRUD del panel
    Route::resource('usuarios', UsuarioController::class)->names('admin.usuarios');
    Route::resource('categorias', CategoriaController::class)->names('admin.categorias');
    Route::resource('colecciones', ColeccionController::class)->parameters([
        'colecciones' => 'coleccion'
    ])->names('admin.colecciones');

    // Rutas de Gestión de Ventas / Pedidos Administrativos
    Route::get('/ventas', [VentaController::class, 'adminIndex'])->name('admin.ventas.index');
    Route::get('/ventas/{id}', [VentaController::class, 'adminShow'])->name('admin.ventas.show');
    Route::patch('/ventas/{id}/estado', [VentaController::class, 'actualizarEstado'])->name('admin.ventas.estado');
    Route::get('/ventas/{id}/factura', [VentaController::class, 'descargarFactura'])->name('admin.ventas.factura');
    
    // Rutas CRUD de Productos (excepto 'index' y 'show' públicos)
    Route::get('productos/{sku_base}/details', [ProductoController::class, 'getDetails'])->name('admin.productos.details');
    Route::post('productos/update-group', [ProductoController::class, 'updateGroup'])->name('admin.productos.updateGroup');
    Route::post('productos/images/upload', [ProductoController::class, 'uploadImage'])->name('admin.productos.images.upload');
    Route::delete('productos/images/{id}', [ProductoController::class, 'deleteImage'])->name('admin.productos.images.delete');
    Route::post('productos/images/{id}/cover', [ProductoController::class, 'setCoverImage'])->name('admin.productos.images.cover');
    Route::resource('productos', ProductoController::class)->except(['index', 'show', 'create'])->names('admin.productos');
});