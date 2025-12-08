<?php

namespace Tests\Feature\Models;

use App\Models\Grupo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GrupoTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_crear_grupo_es_posible(): void
    {
        $grupo = Grupo::factory()->create();

        $this->assertDatabaseHas('grupo', [
            'id' => $grupo->id,
            'codigo' => $grupo->codigo,
            'nombre' => $grupo->nombre,
        ]);
    }

    public function test_respeta_codigo_unico() : void
    {
        $grupo1 = Grupo::factory()->create(['codigo' => 'GRP-001A']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        $grupo2 = Grupo::factory()->create(['codigo' => 'GRP-001A']);
    }
}
