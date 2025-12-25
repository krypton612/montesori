<?php

namespace Tests\Feature\Models;

use App\Models\NotaEstudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Tests\TestCase;

class NotaEstudianteTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_crear_nota_estudiante(): void
    {
        $nota = NotaEstudiante::factory()->create();
        $this->assertDatabaseHas('nota_estudiante', [
            'id' => $nota->id,
        ]);
    }

    public function test_nota_estudiante_relaciones(): void
    {
        $nota = NotaEstudiante::factory()->create();

        $this->assertNotNull($nota->estudiante);
        $this->assertNotNull($nota->evaluacion);
        $this->assertNotNull($nota->estado);
    }

    public function test_nota_estudiante_campos_opcionales(): void
    {
        $nota = NotaEstudiante::factory()->create([
            'nota' => null,
            'observacion' => null,
        ]);

        $this->assertDatabaseHas('nota_estudiante', [
            'id' => $nota->id,
            'nota' => null,
            'observacion' => null,
        ]);
    }

    public function test_nota_estudiante_rango_valido(): void
    {
        $notaValida = NotaEstudiante::factory()->create([
            'nota' => 85.50,
        ]);

        $this->assertDatabaseHas('nota_estudiante', [
            'id' => $notaValida->id,
            'nota' => 85.50,
        ]);
    }

    public function test_nota_estudiante_nota_fuera_de_rango(): void
    {
        $this->expectException(InvalidArgumentException::class);

        NotaEstudiante::factory()->create([
            'nota' => 150.00,
        ]);
    }
}
