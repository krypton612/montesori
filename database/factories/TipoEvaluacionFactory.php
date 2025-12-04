<?php

namespace Database\Factories;

use App\Models\TipoEvaluacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoEvaluacion>
 */
class TipoEvaluacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = TipoEvaluacion::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->word(),
            'descripcion' => $this->faker->sentence(),
            'es_formativa' => $this->faker->boolean(),
            'es_sumativa' => $this->faker->boolean(),
            'visible' => true,
        ];
    }
}
