<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Aula extends Model
{
    use HasFactory;
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
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'aula_id');
    }
}
