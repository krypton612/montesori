<?php

namespace Database\Factories;

use App\Models\DocumentoProfesor;
use App\Models\Profesor;
use App\Models\TipoDocumento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentoProfesor>
 */
class DocumentoProfesorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     protected $model = DocumentoProfesor::class;

    public function definition(): array
    {
        return [
            'nombre_archivo' => $this->faker->word() . '_' . $this->faker->uuid() . '.pdf',
            'tipo_documento_id' => TipoDocumento::factory(),
            'profesor_id' => Profesor::factory(),
        ];
    }

    /**
     * Estado: Documento de título profesional
     */
    public function titulo(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre_archivo' => 'titulo_profesional_' . $this->faker->uuid() . '.pdf',
        ]);
    }

    /**
     * Estado: Documento de certificado
     */
    public function certificado(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre_archivo' => 'certificado_' . $this->faker->uuid() . '.pdf',
        ]);
    }
}
