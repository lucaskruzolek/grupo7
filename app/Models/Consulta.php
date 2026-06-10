<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class Consulta extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $table = 'consultas';
 
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'pedido',
        'asunto',
        'mensaje',
        'leido',
        'respondido',
        'usuario_id',
    ];
 
    protected $casts = [
        'leido' => 'boolean',
        'respondido' => 'boolean',
    ];
 
    /**
     * Una consulta puede pertenecer a un usuario autenticado.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
