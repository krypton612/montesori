<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    /** @use HasFactory<\Database\Factories\EvaluacionFactory> */
    use HasFactory;

    protected $table = 'evaluacion';

    protected $fillable = [
        'tipo_evaluacion_id',
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'curso_id',
        'estado_id',
        'gestion_id',
        'visible'
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'visible' => 'boolean',
        ];
    }

    public function tipoEvaluacion()
    {
        return $this->belongsTo(TipoEvaluacion::class, 'tipo_evaluacion_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'gestion_id');
    }
}
