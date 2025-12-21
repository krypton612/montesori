<?php

namespace Database\Factories;

use App\Models\Estado;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Turno>
 */
class TurnoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Turno::class;

    public function definition(): array
    {
        return [
            'nombre'       => $this->faker->unique()->word(),
            'hora_inicio'  => $this->faker->time('H:i:s'),
            'hora_fin'     => $this->faker->time('H:i:s'),
            'habilitado'   => $this->faker->boolean(80), // 80% de probabilidad de ser true
            'estado_id'    => Estado::factory()->create(), // Asumimos que se asignará un estado válido en otro lugar
        ];
    }
}
