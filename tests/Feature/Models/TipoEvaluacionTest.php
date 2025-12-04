<?php

namespace Tests\Feature\Models;

use App\Models\TipoEvaluacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TipoEvaluacionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_tipo_evaluacion_with_default_attributes()
    {
        $tipoEvaluacion = TipoEvaluacion::factory()->create();

        $this->assertInstanceOf(TipoEvaluacion::class, $tipoEvaluacion);
        $this->assertNotNull($tipoEvaluacion->nombre);
        $this->assertNotNull($tipoEvaluacion->descripcion);
        $this->assertIsBool($tipoEvaluacion->es_formativa);
        $this->assertIsBool($tipoEvaluacion->es_sumativa);
        $this->assertTrue($tipoEvaluacion->visible);
        $this->assertDatabaseHas('tipo_evaluacion', [
            'id' => $tipoEvaluacion->id,
        ]);
    }

    /** @test */
    public function it_creates_multiple_tipo_evaluaciones_with_unique_nombres()
    {
        $tipoEvaluaciones = TipoEvaluacion::factory()->count(3)->create();

        $this->assertCount(3, $tipoEvaluaciones);

        $nombres = $tipoEvaluaciones->pluck('nombre')->toArray();
        $this->assertCount(3, array_unique($nombres));
    }

    /** @test */
    public function it_allows_overriding_default_attributes()
    {
        $tipoEvaluacion = TipoEvaluacion::factory()->create([
            'nombre' => 'Evaluación Parcial',
            'descripcion' => 'Descripción personalizada',
            'es_formativa' => true,
            'es_sumativa' => false,
            'visible' => false,
        ]);

        $this->assertEquals('Evaluación Parcial', $tipoEvaluacion->nombre);
        $this->assertEquals('Descripción personalizada', $tipoEvaluacion->descripcion);
        $this->assertTrue($tipoEvaluacion->es_formativa);
        $this->assertFalse($tipoEvaluacion->es_sumativa);
        $this->assertFalse($tipoEvaluacion->visible);
    }

    /** @test */
    public function it_creates_tipo_evaluacion_as_formativa()
    {
        $tipoEvaluacion = TipoEvaluacion::factory()->create([
            'es_formativa' => true,
            'es_sumativa' => false,
        ]);

        $this->assertTrue($tipoEvaluacion->es_formativa);
        $this->assertFalse($tipoEvaluacion->es_sumativa);
    }

    /** @test */
    public function it_creates_tipo_evaluacion_as_sumativa()
    {
        $tipoEvaluacion = TipoEvaluacion::factory()->create([
            'es_formativa' => false,
            'es_sumativa' => true,
        ]);

        $this->assertFalse($tipoEvaluacion->es_formativa);
        $this->assertTrue($tipoEvaluacion->es_sumativa);
    }

    /** @test */
    public function it_creates_tipo_evaluacion_without_persisting_to_database()
    {
        $tipoEvaluacion = TipoEvaluacion::factory()->make();

        $this->assertInstanceOf(TipoEvaluacion::class, $tipoEvaluacion);
        $this->assertNull($tipoEvaluacion->id);
        $this->assertDatabaseCount('tipo_evaluacion', 0);
    }

    /** @test */
    public function visible_is_true_by_default()
    {
        $tipoEvaluacion = TipoEvaluacion::factory()->create();

        $this->assertTrue($tipoEvaluacion->visible);
    }

    /** @test */
    public function it_generates_different_data_for_each_instance()
    {
        $tipo1 = TipoEvaluacion::factory()->create();
        $tipo2 = TipoEvaluacion::factory()->create();

        $this->assertNotEquals($tipo1->nombre, $tipo2->nombre);
        $this->assertNotEquals($tipo1->descripcion, $tipo2->descripcion);
    }
}
