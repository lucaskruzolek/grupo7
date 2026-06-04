<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'fecha_venta',
        'fecha_despacho',
        'usuario_id',
        'estado',
        'total',
        'forma_pago_id',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'fecha_despacho' => 'datetime',
        'total' => 'decimal:2',
    ];

    // ── Scopes ──────────────────────────────────────────────

    /**
     * Scope para filtrar ventas en estado CARRITO.
     */
    public function scopeCarrito($query)
    {
        return $query->where('estado', 'CARRITO');
    }

    /**
     * Scope para filtrar ventas confirmadas o despachadas.
     */
    public function scopeVentas($query)
    {
        return $query->whereIn('estado', ['CONFIRMADO', 'DESPACHADO']);
    }

    // ── Relaciones ──────────────────────────────────────────

    /**
     * Una venta pertenece a un usuario (si está autenticado).
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Una venta pertenece a una forma de pago.
     */
    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_pago_id');
    }

    /**
     * Una venta tiene muchos detalles.
     */
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }
}
