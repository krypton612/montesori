<?php

namespace Database\Factories;

use App\Models\TipoDocumento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoDocumento>
 */
class TipoDocumentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = TipoDocumento::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->randomElement(['DNI', 'Pasaporte', 'Certificado de nacimiento', 'Licencia de conducir']),
            'descripcion' => $this->faker->sentence(),
            'habilitado' => $this->faker->boolean(90), // 90% de probabilidad de ser true
            'tipo' => $this->faker->randomElement(['documento_profesor', 'certificado', 'otro']),
        ];
    }
}
