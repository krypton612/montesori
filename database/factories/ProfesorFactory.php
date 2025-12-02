<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profesor>
 */
class ProfesorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'persona_id' => Persona::factory(),

            'codigo_saga' => strtoupper($this->faker->bothify('PROF-####')),
            'habilitado' => true,

            'nacionalidad' => $this->faker->randomElement(['Boliviano', 'Peruano', 'Chileno', 'Argentino']),
            'foto_url' => $this->faker->imageUrl(300, 300, 'people'),

            'anios_experiencia' => $this->faker->numberBetween(0, 40),
            'profesion' => $this->faker->randomElement([
                'Profesor de Matemáticas',
                'Profesor de Lenguaje',
                'Profesor de Tecnología',
                'Profesor de Ciencias',
                'Lic. Educación',
                'Ingeniería',
            ]),
        ];
    }
}
