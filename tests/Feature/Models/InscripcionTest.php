<?php

namespace Tests\Feature\Models;

use App\Models\Estado;
use App\Models\Estudiante;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InscripcionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function crea_inscripcion_correctamente()
    {
        $inscripcion = Inscripcion::factory()->create();

        $this->assertDatabaseHas('inscripcion', [
            'id' => $inscripcion->id,
            'codigo_inscripcion' => $inscripcion->codigo_inscripcion,
        ]);
    }

    /** @test */
    public function inscripcion_tiene_relaciones_correctas()
    {
        $inscripcion = Inscripcion::factory()->create();

        $this->assertInstanceOf(Estudiante::class, $inscripcion->estudiante);
        $this->assertInstanceOf(Grupo::class, $inscripcion->grupo);
        $this->assertInstanceOf(Gestion::class, $inscripcion->gestion);
        $this->assertInstanceOf(Estado::class, $inscripcion->estado);
    }
    /** @test */
    public function si_acepta_codigo_duplicado()
    {
        \App\Models\Inscripcion::factory()->create([
            'codigo_inscripcion' => 'INS-0001'
        ]);

        \App\Models\Inscripcion::factory()->create([
            'codigo_inscripcion' => 'INS-0001'
        ]);

        $this->assertDatabaseCount('inscripcion', 2);
    }
    /** @test */
    public function elimina_inscripcion_correctamente()
    {
        $inscripcion = \App\Models\Inscripcion::factory()->create();

        $inscripcion->delete();

        $this->assertDatabaseMissing('inscripcion', [
            'id' => $inscripcion->id
        ]);
    }

    /** @test */
    public function asigna_datos_manualmente_sin_factory()
    {
        $inscripcion = \App\Models\Inscripcion::create([
            'codigo_inscripcion' => 'INS-XYZ',
            'estudiante_id' => \App\Models\Estudiante::factory()->create()->id,
            'curso_id' => \App\Models\Curso::factory()->create()->id,
            'grupo_id' => \App\Models\Grupo::factory()->create()->id,
            'gestion_id' => \App\Models\Gestion::factory()->create()->id,
            'estado_id' => \App\Models\Estado::factory()->create()->id,
            'fecha_inscripcion' => now()->toDateString(),
        ]);

        $this->assertDatabaseHas('inscripcion', [
            'codigo_inscripcion' => 'INS-XYZ',
        ]);
    }

}
