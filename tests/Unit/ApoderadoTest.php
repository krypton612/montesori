<?php

namespace Tests\Feature\Models;

use App\Models\Apoderado;
use App\Models\Persona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApoderadoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crearse_un_apoderado()
    {
        $persona = Persona::factory()->create();

        $apoderado = Apoderado::create([
            'persona_id'      => $persona->id,
            'ocupacion'       => 'Ingeniero',
            'empresa'         => 'Empresa X',
            'cargo_empresa'   => 'Jefe de área',
            'nivel_educacion' => 'Universitario',
            'estado_civil'    => 'Casado',
        ]);

        $this->assertDatabaseHas('apoderado', [
            'id'         => $apoderado->id,
            'persona_id' => $persona->id,
            'ocupacion'  => 'Ingeniero',
        ]);
    }

    /** @test */
    public function apoderado_pertenece_a_una_persona()
    {
        $persona = Persona::factory()->create();

        $apoderado = Apoderado::factory()->create([
            'persona_id' => $persona->id,
        ]);

        $this->assertInstanceOf(Persona::class, $apoderado->persona);
        $this->assertTrue($apoderado->persona->is($persona));
    }

    /** @test */
    public function configuracion_basica_del_modelo_es_correcta()
    {
        $model = new Apoderado();

        $this->assertEquals('apoderado', $model->getTable());

        $this->assertEquals([
            'persona_id',
            'ocupacion',
            'empresa',
            'cargo_empresa',
            'nivel_educacion',
            'estado_civil',
        ], $model->getFillable());

        $casts = $model->getCasts();

        $this->assertArrayHasKey('persona_id', $casts);
        $this->assertEquals('integer', $casts['persona_id']);
    }

    /** @test */
    public function soporta_campos_opcionales_nulos()
    {
        $persona = Persona::factory()->create();

        $apoderado = Apoderado::create([
            'persona_id'      => $persona->id,
            'ocupacion'       => null,
            'empresa'         => null,
            'cargo_empresa'   => null,
            'nivel_educacion' => null,
            'estado_civil'    => null,
        ]);

        $this->assertNull($apoderado->ocupacion);
        $this->assertNull($apoderado->empresa);
        $this->assertNull($apoderado->cargo_empresa);
        $this->assertNull($apoderado->nivel_educacion);
        $this->assertNull($apoderado->estado_civil);

        $this->assertDatabaseHas('apoderado', [
            'id' => $apoderado->id,
        ]);
    }

    /** @test */
    public function puede_hacerse_soft_delete_y_restore_de_un_apoderado()
    {
        $apoderado = Apoderado::factory()->create();

        $apoderado->delete();

        $this->assertSoftDeleted('apoderado', [
            'id' => $apoderado->id,
        ]);

        $apoderado->restore();

        $this->assertDatabaseHas('apoderado', [
            'id' => $apoderado->id,
        ]);

        $this->assertNull($apoderado->fresh()->deleted_at);
    }
}
