<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'parent_id',
        'icono',
    ];

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Una categoría pertenece a una categoría padre (opcional).
     */
    public function parent()
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }

    /**
     * Una categoría tiene muchas categorías hijas (subcategorías).
     */
    public function children()
    {
        return $this->hasMany(Categoria::class, 'parent_id');
    }

    /**
     * Una categoría tiene muchos productos asociados.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
