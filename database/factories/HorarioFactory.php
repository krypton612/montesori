<?php

namespace Database\Factories;

use App\Models\Aula;
use App\Models\Curso;
use App\Models\Horario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Horario>
 */
class HorarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Horario::class;
    public function definition(): array
    {
        return [
            'dia' => $this->faker->randomElement(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']),
            'hora_inicio' => $this->faker->time('H:i'),
            'hora_fin' => $this->faker->time('H:i'),
            'aula_id' => Aula::factory()->create(),
            'curso_id' => Curso::factory()->create(),
        ];
    }
}
