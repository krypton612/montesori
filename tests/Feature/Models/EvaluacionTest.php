<?php

namespace Tests\Feature\Models;

use App\Models\Curso;
use App\Models\Estado;
use App\Models\Evaluacion;
use App\Models\Gestion;
use App\Models\TipoEvaluacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluacionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_evaluacion_with_default_attributes()
    {
        $evaluacion = Evaluacion::factory()->create();

        $this->assertInstanceOf(Evaluacion::class, $evaluacion);
        $this->assertNotNull($evaluacion->titulo);
        $this->assertNotNull($evaluacion->descripcion);
        $this->assertNotNull($evaluacion->fecha_inicio);
        $this->assertNotNull($evaluacion->fecha_fin);
        $this->assertIsBool($evaluacion->visible);
        $this->assertDatabaseHas('evaluacion', [
            'id' => $evaluacion->id,
        ]);
    }

    /** @test */
    public function it_creates_evaluacion_with_related_models()
    {
        $evaluacion = Evaluacion::factory()->create();

        $this->assertInstanceOf(TipoEvaluacion::class, $evaluacion->tipoEvaluacion);
        $this->assertInstanceOf(Curso::class, $evaluacion->curso);
        $this->assertInstanceOf(Estado::class, $evaluacion->estado);
        $this->assertInstanceOf(Gestion::class, $evaluacion->gestion);
    }

    /** @test */
    public function it_creates_evaluacion_with_existing_related_models()
    {
        $tipoEvaluacion = TipoEvaluacion::factory()->create();
        $curso = Curso::factory()->create();
        $estado = Estado::factory()->create();
        $gestion = Gestion::factory()->create();

        $evaluacion = Evaluacion::factory()->create([
            'tipo_evaluacion_id' => $tipoEvaluacion->id,
            'curso_id' => $curso->id,
            'estado_id' => $estado->id,
            'gestion_id' => $gestion->id,
        ]);

        $this->assertEquals($tipoEvaluacion->id, $evaluacion->tipo_evaluacion_id);
        $this->assertEquals($curso->id, $evaluacion->curso_id);
        $this->assertEquals($estado->id, $evaluacion->estado_id);
        $this->assertEquals($gestion->id, $evaluacion->gestion_id);
    }

    /** @test */
    public function it_allows_overriding_default_attributes()
    {
        $evaluacion = Evaluacion::factory()->create([
            'titulo' => 'Evaluación Final',
            'descripcion' => 'Descripción personalizada',
            'fecha_inicio' => '2024-01-15',
            'fecha_fin' => '2024-02-15',
            'visible' => false,
        ]);

        $this->assertEquals('Evaluación Final', $evaluacion->titulo);
        $this->assertEquals('Descripción personalizada', $evaluacion->descripcion);
        $this->assertEquals('2024-01-15', $evaluacion->fecha_inicio->format('Y-m-d'));
        $this->assertEquals('2024-02-15', $evaluacion->fecha_fin->format('Y-m-d'));
        $this->assertFalse($evaluacion->visible);
    }

    /** @test */
    public function fecha_fin_is_after_fecha_inicio()
    {
        $evaluacion = Evaluacion::factory()->create();

        $fechaInicio = \Carbon\Carbon::parse($evaluacion->fecha_inicio);
        $fechaFin = \Carbon\Carbon::parse($evaluacion->fecha_fin);

        $this->assertTrue($fechaFin->greaterThan($fechaInicio));
    }

    /** @test */
    public function it_creates_multiple_evaluaciones()
    {
        $evaluaciones = Evaluacion::factory()->count(5)->create();

        $this->assertCount(5, $evaluaciones);
        $this->assertDatabaseCount('evaluacion', 5);
    }


    /** @test */
    public function it_creates_evaluaciones_for_same_curso()
    {
        $curso = Curso::factory()->create();

        $evaluaciones = Evaluacion::factory()->count(3)->create([
            'curso_id' => $curso->id,
        ]);

        $this->assertCount(3, $evaluaciones);
        $evaluaciones->each(function ($evaluacion) use ($curso) {
            $this->assertEquals($curso->id, $evaluacion->curso_id);
        });
    }

    /** @test */
    public function titulo_is_a_short_sentence()
    {
        $evaluacion = Evaluacion::factory()->create();

        $wordCount = str_word_count($evaluacion->titulo);

        // El factory usa sentence(3), verificamos que sea corto
        $this->assertLessThanOrEqual(5, $wordCount);
    }

    /** @test */
    public function descripcion_is_a_paragraph()
    {
        $evaluacion = Evaluacion::factory()->create();

        $this->assertNotEmpty($evaluacion->descripcion);
        $this->assertGreaterThan(20, strlen($evaluacion->descripcion));
    }
}
