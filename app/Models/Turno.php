<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'turno';

    protected $fillable = [
        'nombre',
        'hora_inicio',
        'hora_fin',
        'habilitado',
        'estado_id',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'habilitado' => 'boolean',
    ];
}
