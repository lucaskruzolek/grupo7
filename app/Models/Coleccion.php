<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coleccion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'colecciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'url_imagen',
    ];

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Una colección tiene muchos productos.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'coleccion_id');
    }
}
