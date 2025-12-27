<?php

namespace Database\Factories;

use App\Models\Estado;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\NotaEstudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotaEstudiante>
 */
class NotaEstudianteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = NotaEstudiante::class;
    public function definition(): array
    {
        return [
            'nota' => $this->faker->randomFloat(2, 0, 100),
            'observacion' => $this->faker->sentence(),
            'estudiante_id' => Estudiante::factory(),
            'evaluacion_id' => Evaluacion::factory(),
            'estado_id' => Estado::factory(),
        ];
    }
}
