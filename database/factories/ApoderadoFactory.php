<?php

namespace Database\Factories;

use App\Models\Apoderado;
use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Apoderado>
 */
class ApoderadoFactory extends Factory
{
    protected $model = Apoderado::class;

    public function definition(): array
    {
        return [
            // Relación obligatoria con Persona
            'persona_id'      => Persona::factory(),

            'ocupacion'       => $this->faker->optional()->jobTitle(),
            'empresa'         => $this->faker->optional()->company(),
            'cargo_empresa'   => $this->faker->optional()->jobTitle(),
            'nivel_educacion' => $this->faker->optional()->randomElement([
                'Primaria',
                'Secundaria',
                'Técnico',
                'Universitario',
                'Postgrado',
            ]),
            'estado_civil'    => $this->faker->optional()->randomElement([
                'Soltero',
                'Casado',
                'Divorciado',
                'Viudo',
                'Unión libre',
            ]),

            'deleted_at'      => null,
        ];
    }

    /**
     * Estado para un apoderado "eliminado" lógicamente.
     */
    public function trashed(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }
}
