<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscapacidadEstudiante extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'discapacidad_estudiante';

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'discapacidad_id',
        'estudiante_id',
        'observacion',
    ];

    /**
     * Casts de atributos.
     *
     * No hay booleanos aquí, pero casteamos los IDs a integer
     * por prolijidad y para cumplir el criterio de "casts" del issue.
     */
    protected $casts = [
        'discapacidad_id' => 'integer',
        'estudiante_id'   => 'integer',
    ];

    /**
     * NOTA IMPORTANTE:
     *
     * Esta es una tabla pivote, por lo tanto NO definimos aquí
     * relaciones tipo belongsTo / belongsToMany.
     *
     * Las relaciones se deben manejar en:
     *  - Estudiante::discapacidades()
     *  - Discapacidad::estudiantes()
     * usando belongsToMany(...) y, si se desea,
     * ->using(DiscapacidadEstudiante::class).
     */
}
