<?php

namespace Database\Factories;

use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Materia>
 */
class MateriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Materia::class;
    public function definition(): array
    {
        return [
            "nombre" => $this->faker->word(),
            "nivel" => $this->faker->numberBetween(1, 10),
            "horas_semanales" => $this->faker->numberBetween(1,10),
            "descripcion" => $this->faker->sentence(),
            "habilitado" => $this->faker->boolean(80),
            'grado' => $this->faker->randomElement(['PEDAGOGIA', 'PRIMARIA', 'SECUNDARIA']),
        ];
    }
}
