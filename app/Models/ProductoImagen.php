<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    protected $table = 'producto_imagenes';

    protected $fillable = [
        'sku_base',
        'url',
        'orden'
    ];

    // Relación inversa: Muchas imágenes corresponden a un grupo de productos (sku_base)
    public function productos()
    {
        return $this->hasMany(Producto::class, 'sku_base', 'sku_base');
    }
}
