<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'url_imagen'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer'
    ];

    protected $hidden = ['deleted_at'];
}
