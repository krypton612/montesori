<?php

namespace Tests\Feature\Models;

use App\Models\Estudiante;
use App\Models\Persona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstudianteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crearse_un_estudiante()
    {
        $persona = Persona::factory()->create();

        $estudiante = Estudiante::create([
            'persona_id'        => $persona->id,
            'codigo_saga'       => 'SAGA-001',
            'estado_academico'  => 'regular',
            'tiene_discapacidad'=> false,
        ]);

        $this->assertDatabaseHas('estudiante', [
            'id'          => $estudiante->id,
            'persona_id'  => $persona->id,
            'codigo_saga' => 'SAGA-001',
        ]);
    }
}
