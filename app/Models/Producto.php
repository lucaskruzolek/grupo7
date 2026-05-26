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
        'color_id',
        'talle_id',
        'nombre',
        'descripcion',
        'tipo_mascota',
        'sku_base',
        'sku_color',
        'sku',
        'stock',
        'stock_minimo',
        'precio'
    ];

    // Relación comercial básica
    public function categoria() { return $this->belongsTo(Categoria::class, 'categoria_id'); }
    public function coleccion() { return $this->belongsTo(Coleccion::class, 'coleccion_id'); }

    // Relación de atributos de variante
    public function color() { return $this->belongsTo(Color::class, 'color_id'); }
    public function talle() { return $this->belongsTo(Talle::class, 'talle_id'); }

    /**
     * Relación Inteligente: Un producto comparte imágenes con otros productos
     * que tengan su mismo SKU_COLOR (mismo modelo y color, distinto talle).
     */
    public function imagenes()
    {
        // Vinculamos usando el 'sku_color' local contra el 'sku_color' de la tabla de imágenes
        return $this->hasMany(ProductoImagen::class, 'sku_color', 'sku_color');
    }

    /**
     * Helper para obtener solo la imagen de portada (orden = 1)
     */
    public function imagenPortada()
    {
        return $this->hasOne(ProductoImagen::class, 'sku_color', 'sku_color')->where('orden', 1);
    }
}