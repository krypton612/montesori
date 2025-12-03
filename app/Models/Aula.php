<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;

class Aula extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'aula';

    // Campos asignables en masa
    protected $fillable = [
        'codigo',
        'numero',
        'capacidad',
        'habilitado',
    ];

    // Casts útiles
    protected $casts = [
        'habilitado' => 'boolean',
        'capacidad' => 'integer',
    ];

    // Relación con Horario
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'horario', 'aula_id', 'curso_id')
                    ->withPivot('dia', 'hora_inicio', 'hora_fin');
    }
}
