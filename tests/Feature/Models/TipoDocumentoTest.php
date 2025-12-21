<?php

namespace Tests\Feature\Models;

use App\Models\TipoDocumento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TipoDocumentoTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_crear_tipo_documento_categoria(): void
    {
        $tipo_documento = TipoDocumento::factory()->create();

        $this->assertDatabaseHas('tipo_documento', [
                    'id' => $tipo_documento->id,
                    'nombre' => $tipo_documento->nombre,
        ]);

        $this->assertInstanceOf(TipoDocumento::class, $tipo_documento);
    }

    public function test_crear_tipo_documento_con_datos_customizados() 
    {
        $tipo_documento = TipoDocumento::factory()->create([
            'nombre' => 'DNI',
            'descripcion' => 'Documento Nacional de Identidad',
            'habilitado' => true,
            'tipo' => 'documento_profesor',
        ]);

        $this->assertEquals('DNI', $tipo_documento->nombre);
        $this->assertEquals('Documento Nacional de Identidad', $tipo_documento->descripcion);
        $this->assertTrue($tipo_documento->habilitado);
        $this->assertEquals('documento_profesor', $tipo_documento->tipo);
    }
}
