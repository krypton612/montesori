<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Profesor;
use App\Models\Persona;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfesorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Crear un profesor con factory
     */
    public function test_profesor_puede_ser_creado(): void
    {
        $profesor = Profesor::factory()->create();

        $this->assertDatabaseHas('profesor', [
            'id' => $profesor->id,
            'codigo_saga' => $profesor->codigo_saga,
        ]);

        $this->assertInstanceOf(Profesor::class, $profesor);
    }

    /**
     * Test: Profesor tiene una persona asociada (relación)
     */
    public function test_profesor_tiene_persona_asociada(): void
    {
        $profesor = Profesor::factory()->create();

        $this->assertInstanceOf(Persona::class, $profesor->persona);
        $this->assertNotNull($profesor->persona_id);
    }

    /**
     * Test: Código SAGA debe ser único
     */
    public function test_codigo_saga_debe_ser_unico(): void
    {
        Profesor::factory()->create([
            'codigo_saga' => 'PROF-1234',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Profesor::factory()->create([
            'codigo_saga' => 'PROF-1234',
        ]);
    }

    /**
     * Test: Profesor está habilitado por defecto
     */
    public function test_profesor_habilitado_por_defecto(): void
    {
        $profesor = Profesor::factory()->create();

        $this->assertTrue($profesor->habilitado);
    }
}