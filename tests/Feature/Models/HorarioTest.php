<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Horario;
use App\Models\Aula;
use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HorarioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Crear un horario con factory
     */
    public function test_horario_puede_ser_creado(): void
    {
        $horario = Horario::factory()->create();

        $this->assertDatabaseHas('horario', [
            'id' => $horario->id,
            'dia' => $horario->dia,
        ]);

        $this->assertInstanceOf(Horario::class, $horario);
    }

    /**
     * Test: Horario tiene aula y curso asociados (relaciones)
     */
    public function test_horario_tiene_relaciones(): void
    {
        $horario = Horario::factory()->create();

        $this->assertInstanceOf(Aula::class, $horario->aula);
        $this->assertInstanceOf(Curso::class, $horario->curso);
        $this->assertNotNull($horario->aula_id);
        $this->assertNotNull($horario->curso_id);
    }

    /**
     * Test: Día es un día válido de la semana
     */
    public function test_dia_es_valido(): void
    {
        $horario = Horario::factory()->create();

        $diasValidos = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $this->assertContains($horario->dia, $diasValidos);
    }

    /**
     * Test: Hora de inicio es antes de hora de fin
     */
    public function test_hora_inicio_antes_de_hora_fin(): void
    {
        $horario = Horario::factory()->create([
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        $this->assertLessThan($horario->hora_fin, $horario->hora_inicio);
    }

    /**
     * Test: Relación pivote - Curso puede tener múltiples aulas
     */
    public function test_curso_puede_tener_multiples_aulas(): void
    {
        $curso = Curso::factory()->create();
        $aula1 = Aula::factory()->create();
        $aula2 = Aula::factory()->create();

        // Attach aulas al curso con datos pivot
        $curso->aulas()->attach($aula1->id, [
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        $curso->aulas()->attach($aula2->id, [
            'dia' => 'Martes',
            'hora_inicio' => '10:00',
            'hora_fin' => '12:00',
        ]);

        $this->assertEquals(2, $curso->aulas()->count());
    }

    /**
     * Test: Relación pivote - Aula puede tener múltiples cursos
     */
    public function test_aula_puede_tener_multiples_cursos(): void
    {
        $aula = Aula::factory()->create();
        $curso1 = Curso::factory()->create();
        $curso2 = Curso::factory()->create();

        // Attach cursos al aula con datos pivot
        $aula->cursos()->attach($curso1->id, [
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        $aula->cursos()->attach($curso2->id, [
            'dia' => 'Lunes',
            'hora_inicio' => '14:00',
            'hora_fin' => '16:00',
        ]);

        $this->assertEquals(2, $aula->cursos()->count());
    }

    /**
     * Test: Datos pivot (withPivot) son accesibles
     */
    public function test_datos_pivot_son_accesibles(): void
    {
        $curso = Curso::factory()->create();
        $aula = Aula::factory()->create();

        // Attach con datos pivot
        $curso->aulas()->attach($aula->id, [
            'dia' => 'Miércoles',
            'hora_inicio' => '09:00',
            'hora_fin' => '11:00',
        ]);

        // Recuperar el aula con datos pivot
        $aulaCurso = $curso->aulas()->first();

        $this->assertEquals('Miércoles', $aulaCurso->pivot->dia);
        $this->assertEquals('09:00', $aulaCurso->pivot->hora_inicio);
        $this->assertEquals('11:00', $aulaCurso->pivot->hora_fin);
    }

    /**
     * Test: Mismo curso no puede tener misma aula en el mismo horario (lógica de negocio)
     */
    public function test_evitar_conflicto_horario_mismo_curso_aula(): void
    {
        $curso = Curso::factory()->create();
        $aula = Aula::factory()->create();

        // Primer horario
        $curso->aulas()->attach($aula->id, [
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        // Verificar que existe el primer horario
        $this->assertEquals(1, $curso->aulas()->count());

        // Intentar agregar mismo horario (esto debería manejarse en la aplicación)
        // Por ahora solo verificamos que podemos consultar
        $horariosLunes = $curso->aulas()
            ->wherePivot('dia', 'Lunes')
            ->wherePivot('hora_inicio', '08:00')
            ->count();

        $this->assertEquals(1, $horariosLunes);
    }

    /**
     * Test: Múltiples horarios para el mismo curso en diferentes días
     */
    public function test_curso_puede_tener_horarios_en_diferentes_dias(): void
    {
        $curso = Curso::factory()->create();
        $aula = Aula::factory()->create();

        // Lunes
        $curso->aulas()->attach($aula->id, [
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        // Miércoles
        $curso->aulas()->attach($aula->id, [
            'dia' => 'Miércoles',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        // Viernes
        $curso->aulas()->attach($aula->id, [
            'dia' => 'Viernes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        $this->assertEquals(3, $curso->aulas()->count());
    }

    /**
     * Test: Detach - eliminar relación pivote
     */
    public function test_puede_eliminar_horario(): void
    {
        $curso = Curso::factory()->create();
        $aula = Aula::factory()->create();

        $curso->aulas()->attach($aula->id, [
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        $this->assertEquals(1, $curso->aulas()->count());

        // Eliminar la relación
        $curso->aulas()->detach($aula->id);

        $this->assertEquals(0, $curso->aulas()->count());
    }
}
