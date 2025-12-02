<?php

namespace Tests\Feature\Models;

use App\Models\Estado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EstadoTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_crear_estado_para_modelos(): void
    {
        $estado = Estado::factory()->create();
        $this->assertDatabaseHas('estado', [
            'id' => $estado->id,
            'nombre' => $estado->nombre,
        ]);

        $this->assertInstanceOf(Estado::class, $estado);
    }

    public function test_crear_estado_con_datos_customizados() 
    {
        $estado = Estado::factory()->create([
            'nombre' => 'Activo',
            'descripcion' => 'Estado activo',
            'habilitado' => true,
            'tipo' => 'inscripcion',
        ]);

        $this->assertEquals('Activo', $estado->nombre);
        $this->assertEquals('Estado activo', $estado->descripcion);
        $this->assertTrue($estado->habilitado);
        $this->assertEquals('inscripcion', $estado->tipo);
    }
}
