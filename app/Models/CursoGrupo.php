<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoGrupo extends Model
{
    protected $table = 'curso_grupo';

    protected $fillable = [
        'curso_id',
        'grupo_id',
    ];

    public $timestamps = true;

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
}
