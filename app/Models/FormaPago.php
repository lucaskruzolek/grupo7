<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    use HasFactory;

    protected $table = 'formas_pago';

    protected $fillable = [
        'descripcion',
    ];

    /**
     * Una forma de pago está asociada a muchas ventas.
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'forma_pago_id');
    }
}
