<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController; 
use App\Http\Controllers\ColeccionController; 
use App\Http\Controllers\AdminController; 

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
    return view('frontend.principal');
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

Route::post('/consultas', function () {
    return view('frontend.exito-consulta');
});

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
    Route::get('/pedidos', [AdminController::class, 'pedidos'])->name('admin.pedidos');
    Route::get('/consultas', [AdminController::class, 'consultas'])->name('admin.consultas');
    Route::get('/clientes', [AdminController::class, 'clientes'])->name('admin.clientes');

    // Listado de productos administrativo (separado del catálogo público)
    Route::get('/productos', [ProductoController::class, 'adminIndex'])->name('admin.productos.index');

    // Recursos CRUD del panel
    Route::resource('usuarios', UsuarioController::class)->names('admin.usuarios');
    Route::resource('categorias', CategoriaController::class)->names('admin.categorias');
    Route::resource('colecciones', ColeccionController::class)->names('admin.colecciones');
    
    // Rutas CRUD de Productos (excepto 'index' y 'show' públicos)
    Route::get('productos/{sku_base}/details', [ProductoController::class, 'getDetails'])->name('admin.productos.details');
    Route::post('productos/update-group', [ProductoController::class, 'updateGroup'])->name('admin.productos.updateGroup');
    Route::post('productos/images/upload', [ProductoController::class, 'uploadImage'])->name('admin.productos.images.upload');
    Route::delete('productos/images/{id}', [ProductoController::class, 'deleteImage'])->name('admin.productos.images.delete');
    Route::post('productos/images/{id}/cover', [ProductoController::class, 'setCoverImage'])->name('admin.productos.images.cover');
    Route::resource('productos', ProductoController::class)->except(['index', 'show', 'create'])->names('admin.productos');
});