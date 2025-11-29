<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class MallaCurricular extends Model
{
    use HasFactory;

    protected $table = 'malla_curricular';

    protected $fillable = [
        'materia_id',
        'anio',
        'nombre_archivo',
        'habilitado',
    ];

    protected $casts = [
        'materia_id' => 'integer',
        'anio' => 'integer',
        'habilitado' => 'boolean',
    ];

    // Relación con Materia
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

}
