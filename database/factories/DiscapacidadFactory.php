<?php

namespace Database\Factories;

use App\Models\Discapacidad;
use App\Models\TipoDiscapacidad;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscapacidadFactory extends Factory
{
    protected $model = Discapacidad::class;

    public function definition(): array
    {
        return [
            // varchar(255)
            'nombre'       => $this->faker->words(3, true),

            // text nullable
            'descripcion'  => $this->faker->optional()->paragraph(),

            // string(50) UNIQUE
            'codigo'       => $this->faker->unique()->bothify('DIS-###'),

            // FK obligatoria a tipo_discapacidad
            'tipo_discapacidad_id' => TipoDiscapacidad::factory(),

            'requiere_acompaniante'        => $this->faker->boolean(30),
            'necesita_equipo_especial'     => $this->faker->boolean(20),
            'requiere_adaptacion_curricular' => $this->faker->boolean(25),
            'visible'                      => true,
        ];
    }
}
