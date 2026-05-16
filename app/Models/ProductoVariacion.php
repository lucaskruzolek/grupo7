<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoVariacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'producto_variaciones';

    protected $fillable = [
        'producto_id',
        'color_id',
        'talle_id',
        'stock',
        'precio_adicional',
        'sku',
    ];

    protected $casts = [
        'stock'            => 'integer',
        'precio_adicional' => 'decimal:2',
    ];

    // ── Accessors ───────────────────────────────────────────

    /**
     * Precio final = precio_base del producto + precio_adicional de la variación.
     */
    public function getPrecioFinalAttribute(): string
    {
        return number_format(
            (float) $this->producto->precio_base + (float) $this->precio_adicional,
            2, '.', ''
        );
    }

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Una variación pertenece a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Una variación pertenece a un color.
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    /**
     * Una variación pertenece a un talle (opcional).
     */
    public function talle()
    {
        return $this->belongsTo(Talle::class, 'talle_id');
    }
}
