<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Discapacidad;
use App\Models\Estudiante;

class DiscapacidadEstudiante extends Pivot
{
    use HasFactory;

    protected $table = 'discapacidad_estudiante';

    /**
     * Importante para que $registro->id NO sea null
     * y Laravel trate la columna "id" como PK autoincremental.
     */
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $fillable = [
        'discapacidad_id',
        'estudiante_id',
        'observacion',
    ];

    protected $casts = [
        'discapacidad_id' => 'integer',
        'estudiante_id'   => 'integer',
    ];

    // Relación inversa hacia Discapacidad
    public function discapacidad()
    {
        return $this->belongsTo(Discapacidad::class, 'discapacidad_id');
    }

    // Relación inversa hacia Estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
