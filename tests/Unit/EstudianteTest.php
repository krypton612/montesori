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
            'persona_id'         => $persona->id,
            'codigo_saga'        => 'SAGA-001',
            'estado_academico'   => 'regular',
            'tiene_discapacidad' => false,
            'observaciones'      => 'Sin observaciones',
            'foto_url'           => 'https://example.com/foto.jpg',
        ]);

        $this->assertDatabaseHas('estudiante', [
            'id'          => $estudiante->id,
            'persona_id'  => $persona->id,
            'codigo_saga' => 'SAGA-001',
        ]);
    }

    /** @test */
    public function estudiante_pertenece_a_una_persona()
    {
        $persona = Persona::factory()->create();

        $estudiante = Estudiante::create([
            'persona_id'         => $persona->id,
            'codigo_saga'        => 'SAGA-002',
            'estado_academico'   => 'regular',
            'tiene_discapacidad' => false,
        ]);

        $this->assertInstanceOf(Persona::class, $estudiante->persona);
        $this->assertTrue($estudiante->persona->is($persona));
    }

    /** @test */
    public function configuracion_basica_del_modelo_es_correcta()
    {
        $estudiante = new Estudiante();

        // nombre de la tabla
        $this->assertEquals('estudiante', $estudiante->getTable());

        // fillable configurados correctamente
        $this->assertEquals(
            [
                'persona_id',
                'codigo_saga',
                'estado_academico',
                'tiene_discapacidad',
                'observaciones',
                'foto_url',
            ],
            $estudiante->getFillable()
        );

        // casts configurados correctamente
        $casts = $estudiante->getCasts();
        $this->assertArrayHasKey('tiene_discapacidad', $casts);
        $this->assertEquals('boolean', $casts['tiene_discapacidad']);
    }

    /** @test */
    public function tiene_discapacidad_se_castea_a_boolean()
    {
        $persona = Persona::factory()->create();

        $estudiante = Estudiante::create([
            'persona_id'         => $persona->id,
            'tiene_discapacidad' => 1, // int
        ]);

        $this->assertIsBool($estudiante->tiene_discapacidad);
        $this->assertTrue($estudiante->tiene_discapacidad);

        $estudiante->tiene_discapacidad = 0;
        $estudiante->save();

        $this->assertFalse($estudiante->fresh()->tiene_discapacidad);
    }

    /** @test */
    public function soporta_campos_opcionales_nulos()
    {
        $persona = Persona::factory()->create();

        $estudiante = Estudiante::create([
            'persona_id'         => $persona->id,
            'codigo_saga'        => null,
            'estado_academico'   => null,
            'tiene_discapacidad' => false,
            'observaciones'      => null,
            'foto_url'           => null,
        ]);

        $this->assertNull($estudiante->codigo_saga);
        $this->assertNull($estudiante->estado_academico);
        $this->assertNull($estudiante->observaciones);
        $this->assertNull($estudiante->foto_url);
    }

    /** @test */
    public function puede_hacerse_soft_delete_y_restore_de_un_estudiante()
    {
        $persona = Persona::factory()->create();

        $estudiante = Estudiante::create([
            'persona_id'         => $persona->id,
            'tiene_discapacidad' => false,
        ]);

        // Soft delete
        $estudiante->delete();

        $this->assertSoftDeleted('estudiante', [
            'id' => $estudiante->id,
        ]);

        // Restaurar
        $estudiante->restore();

        $this->assertDatabaseHas('estudiante', [
            'id' => $estudiante->id,
        ]);

        $this->assertNull($estudiante->fresh()->deleted_at);
    }

    /** @test */
    public function se_puede_actualizar_el_estado_academico()
    {
        $persona = Persona::factory()->create();

        $estudiante = Estudiante::create([
            'persona_id'         => $persona->id,
            'estado_academico'   => 'regular',
            'tiene_discapacidad' => false,
        ]);

        $this->assertEquals('regular', $estudiante->estado_academico);

        $estudiante->update([
            'estado_academico' => 'retirado',
        ]);

        $this->assertEquals('retirado', $estudiante->fresh()->estado_academico);
    }
}
