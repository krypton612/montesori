<?php

namespace Database\Factories;

use App\Models\Grupo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grupo>
 */
class GrupoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Grupo::class;
    public function definition(): array
    {
        return [
            'codigo' => strtoupper($this->faker->bothify('GRP-###??')),
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(),
            'condiciones' => [
                'edad_minima' => $this->faker->numberBetween(10, 18),
                'requisitos' => $this->faker->sentence(),
            ],
            'activo' => $this->faker->boolean(90),
        ];
    }
}
