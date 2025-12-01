<?php

namespace Tests\Unit;

use App\Models\TipoDiscapacidad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TipoDiscapacidadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_un_tipo_discapacidad()
    {
        $tipo = TipoDiscapacidad::create([
            'nombre' => 'Discapacidad Motriz',
            'descripcion' => 'Limitación en la movilidad.',
        ]);

        $this->assertDatabaseHas('tipo_discapacidad', [
            'nombre' => 'Discapacidad Motriz',
        ]);
    }
}
