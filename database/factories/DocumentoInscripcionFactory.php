<?php

namespace Database\Factories;

use App\Models\DocumentoInscripcion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentoInscripcion>
 */
class DocumentoInscripcionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = DocumentoInscripcion::class;
    public function definition(): array
    {
        return [
            'estudiante_id' => \App\Models\Estudiante::factory(),
            'tipo_documento_id' => \App\Models\TipoDocumento::factory(),
            'nombre_archivo' => $this->faker->word() . '.pdf',
            'codigo_inscripcion' => $this->faker->bothify('INS-####'),
        ];
    }
}
