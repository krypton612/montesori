<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;
    // use SoftDeletes; // Descomenta si planeas usar soft deletes

    // Nombre de la tabla (opcional si sigue la convención plural)
    protected $table = 'estado';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        'habilitado',
        'tipo',
    ];

    // Tipos de datos de los atributos (opcional pero recomendado)
    protected $casts = [
        'habilitado' => 'boolean',
    ];
}
