<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subcategorias';

    protected $fillable = [
        'categoria_id',
        'nombre',
    ];

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Una subcategoría pertenece a una categoría.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Una subcategoría tiene muchos productos.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'subcategoria_id');
    }
}
