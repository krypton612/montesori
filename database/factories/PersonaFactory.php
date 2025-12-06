<?php

namespace Database\Factories;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonaFactory extends Factory
{
    protected $model = Persona::class;

    public function definition(): array
    {
        return [
            'usuario_id'           => Usuario::factory()->create(),
            'nombre'               => $this->faker->firstName(),
            'apellido_pat'         => $this->faker->lastName(),
            'apellido_mat'         => $this->faker->lastName(),
            'fecha_nacimiento'     => $this->faker->date(),
            'edad'                 => $this->faker->numberBetween(18, 70),
            'telefono_principal'   => $this->faker->phoneNumber(),
            'telefono_secundario'  => $this->faker->optional()->phoneNumber(),
            'email_personal'       => $this->faker->unique()->safeEmail(),
            'direccion'            => $this->faker->address(),
            'habilitado'           => true,
        ];
    }
}
