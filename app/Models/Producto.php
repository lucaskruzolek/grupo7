<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [ 
        'nombre', 
        'descripcion', 
        'precio', 
        'stock', 
        'url_imagen', 
        'activo', 
    ]; 
 
    protected $casts = [ 
        'precio' => 'decimal:2', 
        'stock' => 'integer', 
        'activo' => 'boolean', 
    ]; 
}
