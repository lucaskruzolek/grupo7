<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talle extends Model
{
    use HasFactory;

    protected $table = 'talles';

    protected $fillable = [
        'nombre',
    ];

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Un talle tiene muchas variaciones de producto.
     */
    public function variaciones()
    {
        return $this->hasMany(ProductoVariacion::class, 'talle_id');
    }
}
