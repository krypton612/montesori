<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupo';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'condiciones', // controla las condiciones de inscripcion.
        'activo',
    ];

    protected $casts = [
        'habilitado' => 'boolean',
        'condiciones' => 'array',
    ];
}
