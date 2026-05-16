<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colores';

    protected $fillable = [
        'nombre',
        'hex_code',
    ];

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Un color tiene muchas variaciones de producto.
     */
    public function variaciones()
    {
        return $this->hasMany(ProductoVariacion::class, 'color_id');
    }

    /**
     * Un color tiene muchas imágenes de producto asociadas.
     */
    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'color_id');
    }
}
