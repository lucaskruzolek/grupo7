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
        'pide_talle',
        'pide_color',
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

    // ── Accesores Virtuales Inteligentes ──────────────────────

    /**
     * Resuelve si la categoría o su padre aceptan variaciones por Talle.
     */
    public function getAceptaTalleAttribute(): bool
    {
        return (bool) ($this->pide_talle ?? $this->parent?->pide_talle ?? true);
    }

    /**
     * Resuelve si la categoría o su padre aceptan variaciones por Color.
     */
    public function getAceptaColorAttribute(): bool
    {
        return (bool) ($this->pide_color ?? $this->parent?->pide_color ?? true);
    }
}
