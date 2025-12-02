<?php

namespace Database\Factories;

use App\Models\Estado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estado>
 */
class EstadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Estado::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->state(),
            'descripcion' => $this->faker->sentence(),
            'habilitado' => $this->faker->boolean(90), // 90% de probabilidad de ser true
            'tipo' => "inscripcion",
        ];
    }

    
}
