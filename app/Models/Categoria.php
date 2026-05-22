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
}
