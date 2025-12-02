<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'curso';

    protected $fillable = [
        'seccion',
        'cupo_maximo',
        'cupo_minimo',
        'cupo_actual',
        'profesor_id',
        'materia_id',
        'estado_id',
        'turno_id',
        'gestion_id',
        'habilitado',
    ];

    protected $casts = [
        'cupo_maximo' => 'integer',
        'cupo_minimo' => 'integer',
        'cupo_actual' => 'integer',
        'habilitado' => 'boolean',
    ];

    // Relaciones
    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

    public function aulas()
    {
        return $this->belongsToMany(Aula::class, 'horario', 'curso_id', 'aula_id')
                    ->withPivot('dia', 'hora_inicio', 'hora_fin');
    }
}
