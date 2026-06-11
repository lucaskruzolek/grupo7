<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    
    protected $table    = 'usuarios';
    protected $fillable = ['nombre', 'apellido', 'email', 'password', 'rol_id', 'telefono', 'direccion', 'localidad', 'provincia', 'codigo_postal'];
    protected $hidden   = ['password', 'remember_token']; // nunca expuestos en JSON
    
    protected $casts = [
        'password' => 'hashed',       // hashea automáticamente al asignar
    ];

    // Relación: un Usuario pertenece a un Rol  →  se usa como $usuario->rol
    public function rol() {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // Relación: un Usuario tiene muchas Ventas  →  se usa como $usuario->ventas
    public function ventas() {
        return $this->hasMany(Venta::class, 'usuario_id');
    }
}
   

