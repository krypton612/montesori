<?php

namespace Tests\Feature\Models;

use App\Models\Gestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GestionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_crear_gestion_con_factory(): void
    {
        $gestion = Gestion::factory()->create();
        $this->assertDatabaseHas('gestion', [
            'id' => $gestion->id,
            'nombre' => $gestion->nombre,
        ]);
    }

    public  function test_crear_gestion_con_datos_customizados(): void
    {
        $gestion = Gestion::factory()->create([
            'nombre' => 'Gestion 2024',
            'habilitado' => true,
        ]);

        $this->assertDatabaseHas('gestion', [
            'id' => $gestion->id,
            'nombre' => 'Gestion 2024',
            'habilitado' => true,
        ]);
    }

    public  function test_crear_gestion_unica(): void
    {
        $gestion = Gestion::factory()->create([
            'nombre' => 'Gestion Unica',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Gestion::factory()->create([
            'nombre' => 'Gestion Unica',
        ]);
    }
}
