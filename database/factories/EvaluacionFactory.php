<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Estado;
use App\Models\Evaluacion;
use App\Models\Gestion;
use App\Models\TipoEvaluacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evaluacion>
 */
class EvaluacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Evaluacion::class;

    public function definition(): array
    {
        return [
            'tipo_evaluacion_id' => TipoEvaluacion::factory()->create(),
            'titulo' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),
            'fecha_inicio' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'fecha_fin' => $this->faker->dateTimeBetween('+1 month', '+3 months')->format('Y-m-d'),
            'curso_id' => Curso::factory()->create(),
            'estado_id' => Estado::factory()->create(),
            'gestion_id' => Gestion::factory()->create(),
            'visible' => $this->faker->boolean(80), // 80% de probabilidad de ser true
        ];
    }
}
