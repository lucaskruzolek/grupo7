<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController; 
use App\Http\Controllers\ColeccionController; 

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


// 2. AUTENTICACIÓN (LOGIN Y REGISTRO)
Route::get('/login', function () {
    return view('backend.usuarios.login');
})->name('login');

Route::get('/register', function () {
    return view('backend.usuarios.register');
})->name('register');


// 3. MOSTRADOR DE PRODUCTOS DINÁMICO (CON FILTROS)
// Carga el controlador para procesar los filtros del sidebar, NO una vista estática
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

// Esta es la ruta encargada de mostrar el detalle de la prenda mediante su código de modelo
Route::get('productos/{sku_base}', [ProductoController::class, 'show'])->name('productos.show');


// 4. MANTENIMIENTO Y CRUD DE ADMINISTRACIÓN (BACKEND)
Route::resource('usuarios', UsuarioController::class);
Route::resource('categorias', CategoriaController::class);
Route::resource('colecciones', ColeccionController::class);

// Rutas CRUD de Productos (excepto 'index' y 'show' que ya configuramos arriba para el cliente)
Route::resource('productos', ProductoController::class)->except(['index', 'show']);