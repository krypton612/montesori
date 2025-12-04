<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Feature\Models\PersonaTest;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estudiantes>
 */
class EstudianteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'persona_id' => Persona::factory()->create(),
            'codigo_saga' => $this->faker->unique()->numerify('SAGA#######'),
            'estado_academico' => $this->faker->randomElement(['activo', 'inactivo', 'graduado', 'suspendido']),
            'tiene_discapacidad' => $this->faker->boolean(20), // 20% de probabilidad de ser true
            'observaciones' => $this->faker->optional()->paragraph(),
            'foto_url' => $this->faker->optional()->imageUrl(200, 200, 'people'),
        ];
    }
}
