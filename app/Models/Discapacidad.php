<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoDiscapacidad;
use App\Models\Estudiante;
use App\Models\DiscapacidadEstudiante;

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
        'requiere_acompaniante'        => 'boolean',
        'necesita_equipo_especial'     => 'boolean',
        'requiere_adaptacion_curricular' => 'boolean',
        'visible'                      => 'boolean',
    ];

    public function tipoDiscapacidad()
    {
        return $this->belongsTo(TipoDiscapacidad::class, 'tipo_discapacidad_id');
    }

    /**
     * Relación N:N con Estudiante usando la tabla pivote discapacidad_estudiante
     */
    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'discapacidad_estudiante')
            ->using(DiscapacidadEstudiante::class)
            ->withPivot('observacion')
            ->withTimestamps();
    }
}
