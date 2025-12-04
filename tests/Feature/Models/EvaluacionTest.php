<?php

namespace Tests\Feature\Models;

use App\Models\Evaluacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EvaluacionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;


    public function test_it_creates_an_evaluacion_with_default_attributes(): void
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
}
