<?php

use Illuminate\Support\Facades\Route;

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