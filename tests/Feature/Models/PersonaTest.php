<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Crear una persona con factory
     */
    public function test_persona_puede_ser_creada(): void
    {
        $persona = Persona::factory()->create();

        $this->assertDatabaseHas('persona', [
            'id' => $persona->id,
            'email_personal' => $persona->email_personal,
        ]);

        $this->assertInstanceOf(Persona::class, $persona);
    }

    /**
     * Test: Persona tiene un usuario asociado (relación)
     */
    public function test_persona_tiene_usuario_asociado(): void
    {
        $persona = Persona::factory()->create();

        $this->assertInstanceOf(Usuario::class, $persona->usuario);
        $this->assertNotNull($persona->usuario_id);
    }

    /**
     * Test: Email personal debe ser único
     */
    public function test_email_personal_debe_ser_unico(): void
    {
        Persona::factory()->create([
            'email_personal' => 'duplicado@example.com',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Persona::factory()->create([
            'email_personal' => 'duplicado@example.com',
        ]);
    }

    /**
     * Test: Persona está habilitada por defecto
     */
    public function test_persona_habilitada_por_defecto(): void
    {
        $persona = Persona::factory()->create();

        $this->assertTrue($persona->habilitado);
    }
}