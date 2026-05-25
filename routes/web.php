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

Route::get('/', function () {
    return view('frontend.principal');
});
Route::get('/contacto', function () {
    return view('frontend.contacto');
});
Route::get('/comercializacion', function () {
    return view('frontend.comercializacion');
});
Route::get('/productos', function () {
    return view('frontend.productos');
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

Route::get('/login', function () {
    return view('backend.usuarios.login');
})->name('login');

Route::get('/register', function () {
    return view('backend.usuarios.register');
})->name('register');

// Ruta principal de la tienda (Catálogo general de Petthreads)
Route::get('/', [ProductoController::class, 'index'])->name('catalogo.index');

// Rutas RESTful unificadas para Usuarios y Productos
Route::resource('usuarios', UsuarioController::class);
// Rutas de administración de Productos (quitamos 'show' del resource para que no se duplique)
Route::resource('productos', ProductoController::class)->except(['index', 'show']);

// Ajuste para que el detalle busque por 'sku_base' en vez del ID numérico
// Esta es la ÚNICA ruta encargada de mostrar el detalle de la prenda mediante su código de modelo
Route::get('productos/{sku_base}', [ProductoController::class, 'show'])->name('productos.show');
Route::resource('categorias', CategoriaController::class);
Route::resource('colecciones', ColeccionController::class);