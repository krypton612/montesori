<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;
    protected $table = 'horario';

    protected $fillable = [
        'dia',
        'hora_inicio',
        'hora_fin',
        'aula_id',
        'curso_id',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    // Relación con Aula
    /*
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id');
    }

    // Relación con Curso
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
    */
    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
