<?php

namespace Tests\Feature\Models;

use App\Models\Materia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MateriaTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_crear_materia_individual_factory(): void
    {
        $materia = Materia::factory()->create();
        $this->assertDatabaseHas('materia', [
            'id' => $materia->id,
            'nombre' => $materia->nombre,
            'nivel' => $materia->nivel,
            'horas_semanales' => $materia->horas_semanales,
            'descripcion' => $materia->descripcion,
            'habilitado' => $materia->habilitado,
        ]);
    }

    public function test_crear_materia_con_datos_customizados(): void
    {
        $materia = Materia::factory()->create([
            'nombre' => 'Matemáticas',
            'nivel' => 5,
            'horas_semanales' => 4,
            'descripcion' => 'Materia de matemáticas avanzadas',
            'habilitado' => true,
        ]);

        $this->assertEquals('Matemáticas', $materia->nombre);
        $this->assertEquals(5, $materia->nivel);
        $this->assertEquals(4, $materia->horas_semanales);
        $this->assertEquals('Materia de matemáticas avanzadas', $materia->descripcion);
        $this->assertTrue($materia->habilitado);
    }
}
