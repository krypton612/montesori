<?php

namespace Tests\Feature\Models;

use App\Models\MallaCurricular;
use App\Models\Materia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MallaCurricularTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_creando_malla_curricular_factory(): void
    {
        $malla_curricular = MallaCurricular::factory()->create();

        $this->assertDatabaseHas('malla_curricular', [
            'id' => $malla_curricular->id,
            'materia_id' => $malla_curricular->materia_id,
            'anio' => $malla_curricular->anio,
            'nombre_archivo' => $malla_curricular->nombre_archivo,
            'habilitado' => $malla_curricular->habilitado,
        ]);
    }

    public function test_creando_malla_curricular_datos_customizados(): void
    {
        $malla_curricular = MallaCurricular::factory()->create([
            'materia_id' => Materia::factory()->create(),
            'anio' => 3,
            'nombre_archivo' => 'malla_2025.pdf',
            'habilitado' => true,
        ]);

        $this->assertDatabaseHas('malla_curricular', [
            'id' => $malla_curricular->id,
            'materia_id' => 1,
            'anio' => 3,
            'nombre_archivo' => 'malla_2025.pdf',
            'habilitado' => true,
        ]);
    }
}
