<?php

namespace Database\Factories;

use App\Models\Discapacidad;
use App\Models\DiscapacidadEstudiante;
use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\DiscapacidadEstudiante>
 */
class DiscapacidadEstudianteFactory extends Factory
{
    protected $model = DiscapacidadEstudiante::class;

    public function definition(): array
    {
        return [
            // Si quieres que siempre cree también estudiante/discapacidad:
            'discapacidad_id' => Discapacidad::factory(),
            'estudiante_id'   => Estudiante::factory(),
            'observacion'     => $this->faker->optional()->sentence(),
        ];
    }
}
