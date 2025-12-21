<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Apoderado;

class Estudiante extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nombre de la tabla asociada.
     *
     * Por convención Laravel usaría "estudiantes",
     * aquí lo forzamos a "estudiante" como en el diseño.
     */
    protected $table = 'estudiante';

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'persona_id',
        'codigo_saga',
        'estado_academico',
        'tiene_discapacidad',
        'observaciones',
        'foto_url',
    ];

    /**
     * Casts de atributos.
     */
    protected $casts = [
        'tiene_discapacidad' => 'boolean',
    ];

    /**
     * Relación: Estudiante pertenece a una Persona.
     * (relación inversa)
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function discapacidades()
    {
        return $this->belongsToMany(Discapacidad::class, 'discapacidad_estudiante')
            ->using(DiscapacidadEstudiante::class) // usa el modelo pivote custom
            ->withPivot('observacion')
            ->withTimestamps();
    }

    public function getDiscapacidadesCountAttribute()
    {
        return $this->discapacidades()->count();
    }

    /**
     * (Opcional) Si quieres acceder directamente a las filas de la pivote:
     */
    public function discapacidadesPivot()
    {
        return $this->hasMany(DiscapacidadEstudiante::class, 'estudiante_id');
    }

    public function apoderados()
    {
        return $this->belongsToMany(Apoderado::class, 'estudiante_apoderado')
            ->withPivot(['parentestco', 'vive_con_el', 'es_principal'])
            ->withTimestamps();
    }

    public function documentos($codigo_inscripcion = null)
    {
        $query = $this->hasMany(DocumentoInscripcion::class, 'estudiante_id');
        if ($codigo_inscripcion) {
            $query->where('codigo_inscripcion', $codigo_inscripcion);
        }
        return $query;
    }
}
