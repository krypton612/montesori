<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Estudiante;
use App\Models\Apoderado;

class EstudianteApoderado extends Model
{
    use HasFactory;

    protected $table = 'estudiante_apoderado';

    protected $fillable = [
        'estudiante_id',
        'apoderado_id',
        'parentestco',
        'vive_con_el',
        'es_principal',
    ];

    protected $casts = [
        'estudiante_id' => 'integer',
        'apoderado_id'  => 'integer',
        'vive_con_el'   => 'boolean',
        'es_principal'  => 'boolean',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function apoderado()
    {
        return $this->belongsTo(Apoderado::class, 'apoderado_id');
    }


    // Tabla pivote: sin relaciones aquí, se manejan con belongsToMany en Estudiante y Apoderado.
}
