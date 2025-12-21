<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripcion';

    protected $fillable = [
        'codigo_inscripcion',
        'estudiante_id',
        'grupo_id',
        'gestion_id',
        'fecha_inscripcion',
        'estado_id',
        'condiciones',
        'curso_id'
    ];

    protected $casts = [
        'fecha_inscripcion' => 'date',
        'condiciones' => 'array',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'gestion_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }


    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoInscripcion::class, 'codigo_inscripcion', 'codigo_inscripcion');
    }
}
