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
        'gestion_id'
    ];

    protected $casts = [
        'habilitado' => 'boolean',
        'condiciones' => 'array',
    ];

    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'curso_grupo', 'grupo_id', 'curso_id');
    }


    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'gestion_id');
    }

    public function inscritos()
    {
        return $this->hasMany(Inscripcion::class, 'grupo_id');
    }
}
