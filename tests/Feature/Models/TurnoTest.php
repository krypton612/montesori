<?php

namespace Tests\Feature\Models;

use App\Models\Estado;
use App\Models\Turno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TurnoTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;
    public function test_crear_turno_con_factory(): void
    {
        $turno = Turno::factory()->create();
        $this->assertDatabaseHas('turno', [
            'id' => $turno->id,
        ]);

    }

    public function test_crear_turno_con_datos_customizados(): void
    {
        $turno = Turno::factory()->create([
            'nombre' => 'Turno Mañana',
            'hora_inicio' => '2025-12-02 08:00:00',
            'hora_fin' => '2025-12-02 12:00:00',
            'habilitado' => 1,
            'estado_id' => Estado::factory()->create()->id
        ]);
        $this->assertDatabaseHas('turno', [
            'id' => $turno->id,
            'nombre' => 'Turno Mañana',
            'hora_inicio' => '2025-12-02 08:00:00',
            'hora_fin' => '2025-12-02 12:00:00',
            'habilitado' => 1,
            'estado_id' => $turno->estado_id
        ]);
    }
}
