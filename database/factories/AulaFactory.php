<?php

namespace Database\Factories;

use App\Models\Aula;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aula>
 */
class AulaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Aula::class;
    public function definition(): array
    {
        return [
            'codigo' => strtoupper($this->faker->bothify('AULA-??##')),
            'numero' => $this->faker->numberBetween(100, 500),
            'capacidad' => $this->faker->numberBetween(20, 100),
            'habilitado' => $this->faker->boolean(),
        ];
    }
}
