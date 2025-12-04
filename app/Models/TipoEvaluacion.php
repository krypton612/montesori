<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEvaluacion extends Model
{
    /** @use HasFactory<\Database\Factories\TipoEvaluacionFactory> */
    use HasFactory;

    /**
     * Nombre de la tabla asociada.
     *
     * Por convención Laravel usaría "tipo_evaluacions",
     * aquí lo forzamos a "tipo_evaluacion" como en el diseño.
     */

    protected $table = 'tipo_evaluacion';

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'es_formativa',
        'es_sumativa',
        'visible',
    ];

    protected function casts(): array
    {
        return [
            'es_formativa' => 'boolean',
            'es_sumativa' => 'boolean',
            'visible' => 'boolean',
        ];
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }
}
