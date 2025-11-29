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

    // Relación con Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    // Relación con Curso
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'turno_id');    
    }
}
