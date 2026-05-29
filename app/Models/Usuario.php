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
    protected $fillable = ['nombre','apellido', 'email', 'password', 'rol_id'];
    protected $hidden   = ['password', 'remember_token']; // nunca expuestos en JSON
    
    protected function casts(): array {
        return [
            'password' => 'hashed',       // hashea automáticamente al asignar
        ];
    }
     // Relación: un Usuario pertenece a un Rol  →  se usa como $usuario->rol
    public function rol() {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}
   

