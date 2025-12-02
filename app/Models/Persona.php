<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory, SoftDeletes;

    // Nombre de la tabla (opcional si no sigue la convención plural)
    protected $table = 'persona';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'usuario_id',
        'nombre',
        'apellido_pat',
        'apellido_mat',
        'fecha_nacimiento',
        'edad',
        'telefono_principal',
        'telefono_secundario',
        'email_personal',
        'direccion',
        'habilitado',
    ];

    // Tipos de datos de los atributos
    protected $casts = [
        'habilitado' => 'boolean',
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function profesor()
    {
        return $this->hasOne(Profesor::class, 'persona_id');
    }
}

