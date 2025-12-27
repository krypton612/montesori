<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaEstudiante extends Model
{
    use HasFactory;

    protected $table = 'nota_estudiante';

    protected $fillable = [
        'nota',
        'observacion',
        'estudiante_id',
        'evaluacion_id',
        'estado_id',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    // evitar que la nota sea menor a 0 o mayor a 100
    public function setNotaAttribute($value)
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException('La nota debe estar entre 0 y 100.');
        }

        $this->attributes['nota'] = $value;
    }
}
