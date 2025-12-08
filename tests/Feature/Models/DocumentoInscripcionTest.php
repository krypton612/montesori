<?php

namespace Tests\Feature\Models;

use App\Models\DocumentoInscripcion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DocumentoInscripcionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_create_documento_inscripcion(): void
    {
        $documento_inscripcion = DocumentoInscripcion::factory()->create();
        $this->assertDatabaseHas('documento_inscripcion', [
            'id' => $documento_inscripcion->id,
            'nombre_archivo' => $documento_inscripcion->nombre_archivo,
        ]);
    }

    public function test_documento_inscripcion_relations(): void
    {
        $documento_inscripcion = DocumentoInscripcion::factory()->create();

        $this->assertNotNull($documento_inscripcion->inscripcion);
        $this->assertNotNull($documento_inscripcion->tipo_documento);
    }

}
