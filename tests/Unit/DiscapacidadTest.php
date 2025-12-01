<?php

namespace Tests\Unit;

use App\Models\Discapacidad;
use App\Models\TipoDiscapacidad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscapacidadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crear_una_discapacidad_y_guardarla_en_bd()
    {
        // 1) Creamos primero un tipo de discapacidad
        $tipo = TipoDiscapacidad::create([
            'nombre' => 'Tipo de prueba',
            'descripcion' => 'Tipo usado para pruebas unitarias',
        ]);

        // 2) Creamos la discapacidad asociada
        $discapacidad = Discapacidad::create([
            'nombre' => 'Discapacidad de prueba',
            'descripcion' => 'Descripcion de prueba',
            'codigo' => 'TEST-001',
            'tipo_discapacidad_id' => $tipo->id,
            'requiere_acompaniante' => true,
            'necesita_equipo_especial' => false,
            'requiere_adaptacion_curricular' => true,
            'visible' => true,
        ]);

        // 3) Verificamos que exista en la BD
        $this->assertDatabaseHas('discapacidad', [
            'codigo' => 'TEST-001',
            'nombre' => 'Discapacidad de prueba',
        ]);

        // 4) Verificamos que la relación belongsTo funcione
        $this->assertInstanceOf(
            TipoDiscapacidad::class,
            $discapacidad->tipoDiscapacidad
        );
        $this->assertEquals('Tipo de prueba', $discapacidad->tipoDiscapacidad->nombre);

        // 5) Verificamos los casts booleanos
        $this->assertIsBool($discapacidad->requiere_acompaniante);
        $this->assertTrue($discapacidad->requiere_acompaniante);
        $this->assertIsBool($discapacidad->visible);
    }
}
