<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    protected $table = 'producto_imagenes';

    protected $fillable = [
        'producto_id',
        'color_id',
        'url',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    // ── Scopes ──────────────────────────────────────────────

    /**
     * Imagen de portada (orden = 0).
     */
    public function scopeDePortada($query)
    {
        return $query->where('orden', 0);
    }

    /**
     * Filtrar imágenes por color.
     */
    public function scopePorColor($query, int $colorId)
    {
        return $query->where('color_id', $colorId);
    }

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Una imagen pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Una imagen puede pertenecer a un color (opcional).
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
