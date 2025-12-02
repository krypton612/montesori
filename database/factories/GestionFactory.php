<?php

namespace Database\Factories;

use App\Models\Gestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gestion>
 */
class GestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Gestion::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->year() . ' - ' . ($this->faker->year() + 1),
            'fecha_inicio' => $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'fecha_fin' => $this->faker->dateTimeBetween('now', '+1 years')->format('Y-m-d'),
            'habilitado' => $this->faker->boolean(80),
        ];
    }
}
