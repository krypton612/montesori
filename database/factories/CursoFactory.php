<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Estado;
use App\Models\Gestion;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Curso>
 */
class CursoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Curso::class;
    public function definition(): array
    {
        return [
            'seccion' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'cupo_maximo' => $this->faker->numberBetween(20, 100),
            'cupo_minimo' => $this->faker->numberBetween(10, 19),
            'cupo_actual' => $this->faker->numberBetween(0, 20),
            'profesor_id' => Profesor::factory()->create(),
            'materia_id' => Materia::factory()->create(),
            'estado_id' => Estado::factory()->create(),
            'turno_id' => Turno::factory()->create(),
            'gestion_id' => Gestion::factory()->create(),
            'habilitado' => $this->faker->boolean(),
        ];
    }
}
