<?php

namespace Database\Factories;

use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MallaCurricular>
 */
class MallaCurricularFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'materia_id' => Materia::factory(),
            'anio' => $this->faker->numberBetween(1, 5),
            'nombre_archivo' => $this->faker->word() . '.pdf',
            'habilitado' => $this->faker->boolean(),
        ];
    }
}
