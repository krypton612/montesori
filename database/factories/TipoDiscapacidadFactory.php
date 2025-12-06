<?php

namespace Database\Factories;

use App\Models\TipoDiscapacidad;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoDiscapacidadFactory extends Factory
{
    protected $model = TipoDiscapacidad::class;

    public function definition(): array
    {
        return [
            'nombre'      => $this->faker->words(2, true),
            'descripcion' => $this->faker->optional()->sentence(8),
        ];
    }
}
