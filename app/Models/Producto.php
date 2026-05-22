<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'coleccion_id',
        'nombre',
        'descripcion',
        'precio_base',
        'tipo_mascota',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
    ];

    protected $hidden = ['deleted_at'];

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Un producto pertenece a una categoría.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Un producto puede pertenecer a una colección.
     */
    public function coleccion()
    {
        return $this->belongsTo(Coleccion::class, 'coleccion_id');
    }

    /**
     * Un producto tiene muchas variaciones (color + talle).
     */
    public function variaciones()
    {
        return $this->hasMany(ProductoVariacion::class, 'producto_id');
    }

    /**
     * Un producto tiene muchas imágenes.
     */
    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id');
    }
}
