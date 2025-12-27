<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profesor extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'profesor';

    protected $fillable = [
        'persona_id',
        'codigo_saga',
        'habilitado',
        'nacionalidad',
        'foto_url',
        'anios_experiencia',
        'profesion',
    ];

    protected $casts = [
        'persona_id' => 'integer',
        'habilitado' => 'boolean',
    ];

    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    // Relación con DocumentoProfesor
    public function documentos()
    {
        return $this->hasMany(DocumentoProfesor::class, 'profesor_id');
    }

    // Relación con Curso
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'profesor_id');
    }


    public function grupos()
    {
        return $this->hasManyThrough(
            Grupo::class,
            Curso::class,
            'profesor_id',  // Foreign key en tabla curso
            'id',           // Foreign key en tabla grupo
            'id',           // Local key en tabla profesor
            'id'            // Local key en tabla curso
        )
        ->join('curso_grupo', 'curso_grupo.grupo_id', '=', 'grupo.id')
        // probable SQLI
        ->where('curso_grupo.curso_id', '=', \DB::raw('curso.id'))
        ->distinct()
        ->select('grupo.*');
    }

    public function getNombreCompletoAttribute()
    {
        return trim($this->persona->nombre . ' ' . $this->persona->apellido_pat . ' ' . $this->persona->apellido_mat);
    }

}
