<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Crear un usuario con factory
     */
    public function test_usuario_puede_ser_creado_con_factory(): void
    {
        // Crear un usuario con datos aleatorios
        $usuario = Usuario::factory()->create();

        $this->assertDatabaseHas('users', [
            'email' => $usuario->email,
        ]);

        $this->assertInstanceOf(Usuario::class, $usuario);
    }

    public function test_usuario_con_datos_especificos(): void
    {
        $usuario = Usuario::factory()->create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
        ]);

        $this->assertEquals('Juan Pérez', $usuario->name);
        $this->assertEquals('juan@example.com', $usuario->email);
    }

    /**
     * Test: Email debe ser único
     */
    public function test_email_debe_ser_unico(): void
    {
        Usuario::factory()->create([
            'email' => 'duplicado@example.com',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Usuario::factory()->create([
            'email' => 'duplicado@example.com',
        ]);
    }
}