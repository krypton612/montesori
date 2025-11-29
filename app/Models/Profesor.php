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
}
