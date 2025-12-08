<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materia';

    protected $fillable = [
        'nombre',
        'nivel',
        'horas_semanales',
        'descripcion',
        'habilitado',
        'grado',
    ];

    protected $casts = [
        'nivel' => 'integer',
        'horas_semanales' => 'integer',
        'habilitado' => 'boolean',
    ];

    // Relación con malla curricular
    public function mallas()
    {
        return $this->hasMany(MallaCurricular::class, 'materia_id');
    }

    // Relación con cursos
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'materia_id');
    }
}
