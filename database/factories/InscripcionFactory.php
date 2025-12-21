<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Estado;
use App\Models\Estudiante;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inscripcion>
 */
class InscripcionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Inscripcion::class;
    public function definition(): array
    {
        return [
            'codigo_inscripcion' => $this->faker->unique()->bothify('INS-####'),
            'estudiante_id' => Estudiante::factory(),
            'grupo_id' => Grupo::factory(),
            'gestion_id' => Gestion::factory(),
            'curso_id' => Curso::factory(),
            'fecha_inscripcion' => $this->faker->date(),
            'estado_id' => Estado::factory(),
        ];
    }
}
