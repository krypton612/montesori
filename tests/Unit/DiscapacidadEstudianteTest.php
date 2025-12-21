<?php

namespace Tests\Feature\Models;

use App\Models\Discapacidad;
use App\Models\DiscapacidadEstudiante;
use App\Models\Estudiante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscapacidadEstudianteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crearse_un_registro_via_factory_con_relaciones_validas()
    {
        $registro = DiscapacidadEstudiante::factory()->create([
            'observacion' => 'Observación desde factory',
        ]);

        $this->assertDatabaseHas('discapacidad_estudiante', [
            'discapacidad_id' => $registro->discapacidad_id,
            'estudiante_id'   => $registro->estudiante_id,
            'observacion'     => 'Observación desde factory',
        ]);

        $this->assertNotNull($registro->discapacidad_id);
        $this->assertNotNull($registro->estudiante_id);
    }

    /** @test */
    public function permite_campos_nullable_en_nulo()
    {
        $registro = DiscapacidadEstudiante::create([
            'discapacidad_id' => null,
            'estudiante_id'   => null,
            'observacion'     => null,
        ]);

        $this->assertNull($registro->discapacidad_id);
        $this->assertNull($registro->estudiante_id);
        $this->assertNull($registro->observacion);

        $this->assertDatabaseHas('discapacidad_estudiante', [
            'id' => $registro->id,
        ]);
    }

    /** @test */
    public function configuracion_basica_del_modelo_es_correcta()
    {
        $model = new DiscapacidadEstudiante();

        // Nombre de tabla
        $this->assertEquals('discapacidad_estudiante', $model->getTable());

        // Fillable
        $this->assertEquals(
            ['discapacidad_id', 'estudiante_id', 'observacion'],
            $model->getFillable()
        );

        // Casts
        $casts = $model->getCasts();

        $this->assertArrayHasKey('discapacidad_id', $casts);
        $this->assertArrayHasKey('estudiante_id', $casts);
        $this->assertEquals('integer', $casts['discapacidad_id']);
        $this->assertEquals('integer', $casts['estudiante_id']);
    }

    /** @test */
    public function los_ids_se_castean_a_integer()
    {
        $discapacidad = Discapacidad::factory()->create();
        $estudiante   = Estudiante::factory()->create();

        $registro = DiscapacidadEstudiante::factory()->create([
            'discapacidad_id' => (string) $discapacidad->id,
            'estudiante_id'   => (string) $estudiante->id,
            'observacion'     => 'Prueba de casts',
        ]);

        $this->assertIsInt($registro->discapacidad_id);
        $this->assertIsInt($registro->estudiante_id);
    }

    /** @test */
    public function puede_ser_usado_como_pivote_en_relacion_belongs_to_many()
    {
        /**
         * Este test asume que tienes en tus modelos algo como:
         *
         * // En App\Models\Estudiante
         * public function discapacidades()
         * {
         *     return $this->belongsToMany(Discapacidad::class, 'discapacidad_estudiante')
         *         ->withPivot('observacion')
         *         ->withTimestamps();
         * }
         *
         * // En App\Models\Discapacidad
         * public function estudiantes()
         * {
         *     return $this->belongsToMany(Estudiante::class, 'discapacidad_estudiante')
         *         ->withPivot('observacion')
         *         ->withTimestamps();
         * }
         */

        $discapacidad = Discapacidad::factory()->create();
        $estudiante   = Estudiante::factory()->create();

        $estudiante->discapacidades()->attach($discapacidad->id, [
            'observacion' => 'Asignada por relación',
        ]);

        $this->assertDatabaseHas('discapacidad_estudiante', [
            'discapacidad_id' => $discapacidad->id,
            'estudiante_id'   => $estudiante->id,
            'observacion'     => 'Asignada por relación',
        ]);

        $discapacidadDesdeEstudiante = $estudiante->discapacidades()->first();

        $this->assertNotNull($discapacidadDesdeEstudiante);
        $this->assertEquals($discapacidad->id, $discapacidadDesdeEstudiante->id);
        $this->assertEquals(
            'Asignada por relación',
            $discapacidadDesdeEstudiante->pivot->observacion
        );
    }

    /** @test */
    public function puede_existir_mas_de_una_discapacidad_para_el_mismo_estudiante()
    {
        $estudiante    = Estudiante::factory()->create();
        $discapacidadA = Discapacidad::factory()->create();
        $discapacidadB = Discapacidad::factory()->create();

        DiscapacidadEstudiante::create([
            'discapacidad_id' => $discapacidadA->id,
            'estudiante_id'   => $estudiante->id,
            'observacion'     => 'Primera discapacidad',
        ]);

        DiscapacidadEstudiante::create([
            'discapacidad_id' => $discapacidadB->id,
            'estudiante_id'   => $estudiante->id,
            'observacion'     => 'Segunda discapacidad',
        ]);

        $this->assertDatabaseCount('discapacidad_estudiante', 2);

        $this->assertDatabaseHas('discapacidad_estudiante', [
            'discapacidad_id' => $discapacidadA->id,
            'estudiante_id'   => $estudiante->id,
        ]);

        $this->assertDatabaseHas('discapacidad_estudiante', [
            'discapacidad_id' => $discapacidadB->id,
            'estudiante_id'   => $estudiante->id,
        ]);
    }
}
