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
    return view('principal');
});
Route::get('/contacto', function () {
    return view('contacto');
});
Route::get('/comercializacion', function () {
    return view('comercializacion');
});
Route::get('/productos', function () {
    return view('productos');
});
Route::get('/quienes-somos', function () {
    return view('quienes-somos');
});
Route::get('/terminos', function () {
    return view('terminos-de-uso');
});