<?php

namespace Tests\Feature\Models;

use App\Models\Apoderado;
use App\Models\Estudiante;
use App\Models\EstudianteApoderado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstudianteApoderadoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crearse_un_registro_via_factory_con_relaciones_validas()
    {
        $registro = EstudianteApoderado::factory()->create([
            'parentestco' => 'Padre',
        ]);

        $this->assertDatabaseHas('estudiante_apoderado', [
            'id'            => $registro->id,
            'estudiante_id' => $registro->estudiante_id,
            'apoderado_id'  => $registro->apoderado_id,
            'parentestco'   => 'Padre',
        ]);
    }

    /** @test */
    public function configuracion_basica_del_modelo_es_correcta()
    {
        $model = new EstudianteApoderado();

        $this->assertEquals('estudiante_apoderado', $model->getTable());

        $this->assertEquals(
            [
                'estudiante_id',
                'apoderado_id',
                'parentestco',
                'vive_con_el',
                'es_principal',
            ],
            $model->getFillable()
        );

        $casts = $model->getCasts();

        $this->assertEquals('integer', $casts['estudiante_id']);
        $this->assertEquals('integer', $casts['apoderado_id']);
        $this->assertEquals('boolean', $casts['vive_con_el']);
        $this->assertEquals('boolean', $casts['es_principal']);
    }

    /** @test */
    public function booleans_se_castean_correctamente()
    {
        $registro = EstudianteApoderado::factory()->create([
            'vive_con_el'  => 1,
            'es_principal' => 0,
        ]);

        $this->assertTrue($registro->vive_con_el);
        $this->assertFalse($registro->es_principal);
    }

    /** @test */
    public function puede_ser_usado_como_pivote_en_relacion_belongs_to_many()
    {
        /**
         * Requiere que tengas:
         *
         * Estudiante::apoderados()
         * Apoderado::estudiantes()
         * configurados con belongsToMany usando la tabla estudiante_apoderado.
         */

        $estudiante = Estudiante::factory()->create();
        $apoderado  = Apoderado::factory()->create();

        $estudiante->apoderados()->attach($apoderado->id, [
            'parentestco'  => 'Madre',
            'vive_con_el'  => true,
            'es_principal' => true,
        ]);

        $this->assertDatabaseHas('estudiante_apoderado', [
            'estudiante_id' => $estudiante->id,
            'apoderado_id'  => $apoderado->id,
            'parentestco'   => 'Madre',
        ]);

        $apoderadoDesdeEstudiante = $estudiante->apoderados()->first();
        $this->assertNotNull($apoderadoDesdeEstudiante);
        $this->assertEquals($apoderado->id, $apoderadoDesdeEstudiante->id);
        $this->assertEquals(
            'Madre',
            $apoderadoDesdeEstudiante->pivot->parentestco
        );

        // casteamos a boolean para que la aserción sea estricta
        $this->assertTrue((bool) $apoderadoDesdeEstudiante->pivot->vive_con_el);
        $this->assertTrue((bool) $apoderadoDesdeEstudiante->pivot->es_principal);
    }
}
