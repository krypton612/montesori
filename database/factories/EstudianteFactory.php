<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estudiante>
 */
class EstudianteFactory extends Factory
{
    /**
     * El modelo asociado a este factory.
     *
     * @var class-string<\App\Models\Estudiante>
     */
    protected $model = Estudiante::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Relación obligatoria: cada Estudiante necesita una Persona
            'persona_id'         => Persona::factory(),

            // Campos propios
            'codigo_saga'        => $this->faker->optional()->bothify('SAGA-####'),
            'estado_academico'   => $this->faker->randomElement([
                'regular',
                'becado',
                'retirado',
                'egresado',
            ]),
            'tiene_discapacidad' => $this->faker->boolean(20), // ~20% true
            'observaciones'      => $this->faker->optional()->sentence(),
            'foto_url'           => $this->faker->optional()->imageUrl(400, 400, 'people', true),

            // Soft delete
            'deleted_at'         => null,
        ];
    }

    /**
     * Estado para un estudiante "eliminado" (soft deleted).
     */
    public function trashed(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }
}
