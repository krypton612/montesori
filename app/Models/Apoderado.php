<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Persona;
use App\Models\Estudiante;

class Apoderado extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nombre de la tabla asociada.
     */
    protected $table = 'apoderado';

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'persona_id',
        'ocupacion',
        'empresa',
        'cargo_empresa',
        'nivel_educacion',
        'estado_civil',
    ];

    /**
     * Casts de atributos (no hay booleanos aquí, por ahora no son necesarios).
     */
    protected $casts = [
        'persona_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relación: Apoderado pertenece a una Persona (inversa).
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Relación N a N con Estudiante a través de la tabla pivote estudiante_apoderado.
     *
     * NOTA:
     *  - La tabla pivote se espera: estudiante_apoderado
     *  - Campos de pivote (según lo que ya venías usando):
     *      parentestco, vive_con_el, es_principal
     */
    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_apoderado')
            ->withPivot(['parentestco', 'vive_con_el', 'es_principal'])
            ->withTimestamps();
    }
}
