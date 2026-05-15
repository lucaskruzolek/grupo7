<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'roles';           // sobreescribe la pluralización en inglés ('rols')
    protected $fillable = [               // columnas permitidas para asignación masiva
        'nombre', 'descripcion',
    ];
}
    // Relación: un Rol tiene muchos Usuarios  →  se usa como $rol->usuarios
    public function usuarios() {
        return $this->hasMany(Usuario::class, 'rol_id');
    
}
