<?php

namespace Tests\Feature\Models;

use App\Models\Aula;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AulaTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;


    public function test_crear_aula_con_factory(): void
    {
        $aula = Aula::factory()->create();
        $this->assertDatabaseHas('aula', [
            'id' => $aula->id,
            'codigo' => $aula->codigo,
            'numero' => $aula->numero,
            'capacidad' => $aula->capacidad,
            'habilitado' => $aula->habilitado,
        ]);
    }

    public function test_crear_aula_con_datos_customizados(): void
    {
        $aula = Aula::factory()->create([
            'codigo' => 'AULA-101',
            'numero' => '101',
            'capacidad' => 30,
            'habilitado' => true,
        ]);

        $this->assertEquals('AULA-101', $aula->codigo);
        $this->assertEquals('101', $aula->numero);
        $this->assertEquals(30, $aula->capacidad);
        $this->assertTrue($aula->habilitado);
    }

    public function test_crear_aula_con_codigo_unico(): void
    {
        $aula = Aula::factory()->create([
            'codigo' => 'AULA-AB12',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Aula::factory()->create([
            'codigo' => 'AULA-AB12',
        ]);

    }
}
