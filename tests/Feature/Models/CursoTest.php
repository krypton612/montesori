<?php

namespace Tests\Feature\Models;

use App\Models\Curso;
use App\Models\Estado;
use App\Models\Gestion;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Turno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CursoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Crear un curso con factory
     */
    public function test_curso_puede_ser_creado(): void
    {
        $curso = Curso::factory()->create();

        $this->assertDatabaseHas('curso', [
            'id' => $curso->id,
            'seccion' => $curso->seccion,
        ]);

        $this->assertInstanceOf(Curso::class, $curso);
    }

    /**
     * Test: Curso tiene todas las relaciones asociadas
     */
    public function test_curso_tiene_todas_las_relaciones(): void
    {
        $curso = Curso::factory()->create();

        $this->assertInstanceOf(Profesor::class, $curso->profesor);
        $this->assertInstanceOf(Materia::class, $curso->materia);
        $this->assertInstanceOf(Estado::class, $curso->estado);
        $this->assertInstanceOf(Turno::class, $curso->turno);
        $this->assertInstanceOf(Gestion::class, $curso->gestion);

        $this->assertNotNull($curso->profesor_id);
        $this->assertNotNull($curso->materia_id);
        $this->assertNotNull($curso->estado_id);
        $this->assertNotNull($curso->turno_id);
        $this->assertNotNull($curso->gestion_id);
    }

    /**
     * Test: Cupo actual no puede exceder cupo máximo (validación de lógica)
     */
    public function test_cupo_actual_no_excede_cupo_maximo(): void
    {
        $curso = Curso::factory()->create([
            'cupo_maximo' => 30,
            'cupo_actual' => 25,
        ]);

        $this->assertLessThanOrEqual($curso->cupo_maximo, $curso->cupo_actual);
    }

    /**
     * Test: Cupo mínimo es menor que cupo máximo
     */
    public function test_cupo_minimo_menor_que_cupo_maximo(): void
    {
        $curso = Curso::factory()->create([
            'cupo_maximo' => 30,
            'cupo_minimo' => 10,
        ]);

        $this->assertLessThan($curso->cupo_maximo, $curso->cupo_minimo);
    }

    /**
     * Test: Sección es una letra válida
     */
    public function test_seccion_es_letra_valida(): void
    {
        $curso = Curso::factory()->create([
            'seccion' => 'B',
        ]);

        $this->assertContains($curso->seccion, ['A', 'B', 'C', 'D']);
        $this->assertEquals(1, strlen($curso->seccion));
    }

    /**
     * Test: Múltiples cursos pueden tener el mismo profesor
     */
    public function test_profesor_puede_tener_multiples_cursos(): void
    {
        $profesor = Profesor::factory()->create();

        Curso::factory()->count(3)->create([
            'profesor_id' => $profesor->id,
        ]);

        $this->assertEquals(3, Curso::where('profesor_id', $profesor->id)->count());
    }

    /**
     * Test: Crear curso con datos específicos
     */
    public function test_crear_curso_con_datos_especificos(): void
    {
        $profesor = Profesor::factory()->create();
        $materia = Materia::factory()->create();

        $curso = Curso::factory()->create([
            'seccion' => 'A',
            'cupo_maximo' => 40,
            'cupo_minimo' => 15,
            'cupo_actual' => 20,
            'profesor_id' => $profesor->id,
            'materia_id' => $materia->id,
            'habilitado' => true,
        ]);

        $this->assertEquals('A', $curso->seccion);
        $this->assertEquals(40, $curso->cupo_maximo);
        $this->assertEquals(15, $curso->cupo_minimo);
        $this->assertEquals(20, $curso->cupo_actual);
        $this->assertTrue($curso->habilitado);
        $this->assertEquals($profesor->id, $curso->profesor_id);
        $this->assertEquals($materia->id, $curso->materia_id);
    }

    /**
     * Test: Curso puede estar habilitado o deshabilitado
     */
    public function test_curso_puede_estar_habilitado_o_deshabilitado(): void
    {
        $cursoHabilitado = Curso::factory()->create([
            'habilitado' => true,
        ]);

        $cursoDeshabilitado = Curso::factory()->create([
            'habilitado' => false,
        ]);

        $this->assertTrue($cursoHabilitado->habilitado);
        $this->assertFalse($cursoDeshabilitado->habilitado);
    }
}
