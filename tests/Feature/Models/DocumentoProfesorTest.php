<?php

namespace Tests\Feature\Models;

use App\Models\DocumentoProfesor;
use App\Models\Profesor;
use App\Models\TipoDocumento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentoProfesorTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /**
     * Test: Crear un documento de profesor con factory
     */
    public function test_documento_profesor_puede_ser_creado(): void
    {
        $documento = DocumentoProfesor::factory()->create();

        $this->assertDatabaseHas('documento_profesor', [
            'id' => $documento->id,
            'nombre_archivo' => $documento->nombre_archivo,
        ]);

        $this->assertInstanceOf(DocumentoProfesor::class, $documento);
    }

    /**
     * Test: Documento tiene profesor y tipo documento asociados (relaciones)
     */
    public function test_documento_tiene_relaciones(): void
    {
        $documento = DocumentoProfesor::factory()->create();

        $this->assertInstanceOf(Profesor::class, $documento->profesor);
        $this->assertInstanceOf(TipoDocumento::class, $documento->tipoDocumento);
        $this->assertNotNull($documento->profesor_id);
        $this->assertNotNull($documento->tipo_documento_id);
    }

    /**
     * Test: Documento puede ser eliminado (soft delete)
     */
    public function test_documento_puede_ser_eliminado_soft_delete(): void
    {
        $documento = DocumentoProfesor::factory()->create();
        $documentoId = $documento->id;

        $documento->delete();

        $this->assertSoftDeleted('documento_profesor', [
            'id' => $documentoId,
        ]);

        // Verifica que aún existe con trashed
        $this->assertNotNull(DocumentoProfesor::withTrashed()->find($documentoId));
    }

    /**
     * Test: Múltiples documentos pueden pertenecer al mismo profesor
     */
    public function test_profesor_puede_tener_multiples_documentos(): void
    {
        $profesor = Profesor::factory()->create();

        DocumentoProfesor::factory()->count(3)->create([
            'profesor_id' => $profesor->id,
        ]);

        $this->assertEquals(3, DocumentoProfesor::where('profesor_id', $profesor->id)->count());
    }
}
