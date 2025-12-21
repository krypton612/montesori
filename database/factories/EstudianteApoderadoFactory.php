<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\Apoderado;
use App\Models\EstudianteApoderado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EstudianteApoderado>
 */
class EstudianteApoderadoFactory extends Factory
{
    protected $model = EstudianteApoderado::class;

    public function definition(): array
    {
        return [
            'estudiante_id' => Estudiante::factory(),
            'apoderado_id'  => Apoderado::factory(),
            'parentestco'   => $this->faker->randomElement([
                'Padre',
                'Madre',
                'Tutor',
                'Tía',
                'Tío',
                'Hermano',
                'Otro',
            ]),
            'vive_con_el'   => $this->faker->boolean(70), // ~70% true
            'es_principal'  => $this->faker->boolean(60), // ~60% true
        ];
    }
}
