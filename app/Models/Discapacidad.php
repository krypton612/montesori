<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discapacidad extends Model
{
    use HasFactory;

    protected $table = 'discapacidad';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'tipo_discapacidad_id',
        'requiere_acompaniante',
        'necesita_equipo_especial',
        'requiere_adaptacion_curricular',
        'visible',
    ];

    protected $casts = [
        'requiere_acompaniante' => 'boolean',
        'necesita_equipo_especial' => 'boolean',
        'requiere_adaptacion_curricular' => 'boolean',
        'visible' => 'boolean',
    ];

    public function tipoDiscapacidad()
    {
        return $this->belongsTo(TipoDiscapacidad::class, 'tipo_discapacidad_id');
    }

    public function estudiantes()
    {
        // asumiendo modelo Estudiante en App\Models\Estudiante
        return $this->belongsToMany(Estudiante::class, 'discapacidad_estudiante')
            ->withTimestamps();
    }
}
