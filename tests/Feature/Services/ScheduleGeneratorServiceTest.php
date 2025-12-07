<?php

namespace Tests\Feature\Services;

use App\Models\Aula;
use App\Models\Curso;
use App\Models\Horario;
use App\Models\Materia;
use App\Services\ScheduleGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    private ScheduleGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ScheduleGeneratorService;
    }

    /**
     * Test: El servicio puede ser instanciado
     */
    public function test_service_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ScheduleGeneratorService::class, $this->service);
    }

    /**
     * Test: Genera horarios para un curso simple
     */
    public function test_generates_schedules_for_single_course(): void
    {
        // Crear datos de prueba
        $aula = Aula::factory()->create(['capacidad' => 30, 'habilitado' => true]);
        $materia = Materia::factory()->create(['horas_semanales' => 4]);
        $curso = Curso::factory()->create([
            'materia_id' => $materia->id,
            'cupo_actual' => 20,
        ]);

        // Generar horarios
        $result = $this->service->generateSchedules(collect([$curso]));

        // Assertions
        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['schedules']);
        $this->assertCount(4, $result['schedules']); // 4 horas semanales
        $this->assertEmpty($result['conflicts']);
    }

    /**
     * Test: Genera horarios para múltiples cursos
     */
    public function test_generates_schedules_for_multiple_courses(): void
    {
        // Crear aulas
        Aula::factory()->count(3)->create(['capacidad' => 30, 'habilitado' => true]);

        // Crear materias y cursos
        $materias = Materia::factory()->count(3)->create(['horas_semanales' => 3]);
        $cursos = collect();

        foreach ($materias as $materia) {
            $cursos->push(Curso::factory()->create([
                'materia_id' => $materia->id,
                'cupo_actual' => 25,
            ]));
        }

        // Generar horarios
        $result = $this->service->generateSchedules($cursos);

        // Assertions
        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['schedules']);
        $this->assertCount(9, $result['schedules']); // 3 cursos x 3 horas
    }

    /**
     * Test: Detecta conflicto cuando no hay aulas disponibles
     */
    public function test_detects_conflict_when_no_classrooms_available(): void
    {
        // Crear curso sin aulas habilitadas
        $materia = Materia::factory()->create(['horas_semanales' => 2]);
        $curso = Curso::factory()->create(['materia_id' => $materia->id]);

        // Todas las aulas deshabilitadas
        Aula::factory()->count(2)->create(['habilitado' => false]);

        // Generar horarios
        $result = $this->service->generateSchedules(collect([$curso]));

        // Assertions
        $this->assertFalse($result['success']);
        $this->assertEmpty($result['schedules']);
        $this->assertNotEmpty($result['conflicts']);
    }

    /**
     * Test: Aplica horarios a la base de datos
     */
    public function test_applies_schedules_to_database(): void
    {
        // Crear datos de prueba
        $aula = Aula::factory()->create(['capacidad' => 30, 'habilitado' => true]);
        $materia = Materia::factory()->create(['horas_semanales' => 2]);
        $curso = Curso::factory()->create(['materia_id' => $materia->id]);

        // Generar horarios
        $result = $this->service->generateSchedules(collect([$curso]));

        // Aplicar a la base de datos
        $applied = $this->service->applySchedules($result['schedules']);

        $this->assertTrue($applied);

        // Verificar que se guardaron en la base de datos
        $this->assertEquals(2, Horario::count());

        $firstSchedule = $result['schedules'][0];
        $this->assertDatabaseHas('horario', [
            'curso_id' => $firstSchedule['curso_id'],
            'aula_id' => $firstSchedule['aula_id'],
            'dia' => $firstSchedule['dia'],
        ]);
    }

    /**
     * Test: Valida horarios existentes y detecta conflictos
     */
    public function test_validates_existing_schedules_and_detects_conflicts(): void
    {
        // Crear datos de prueba
        $aula = Aula::factory()->create(['capacidad' => 30, 'habilitado' => true]);
        $curso1 = Curso::factory()->create();
        $curso2 = Curso::factory()->create();

        // Crear conflicto intencional: misma aula, mismo horario
        Horario::factory()->create([
            'curso_id' => $curso1->id,
            'aula_id' => $aula->id,
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        Horario::factory()->create([
            'curso_id' => $curso2->id,
            'aula_id' => $aula->id,
            'dia' => 'Lunes',
            'hora_inicio' => '09:00',
            'hora_fin' => '11:00',
        ]);

        // Validar
        $result = $this->service->validateExistingSchedules();

        // Assertions
        $this->assertNotEmpty($result['conflicts']);
        $this->assertGreaterThan(0, $result['statistics']['classroom_conflicts']);
    }

    /**
     * Test: Valida horarios sin conflictos
     */
    public function test_validates_schedules_without_conflicts(): void
    {
        // Crear datos sin conflictos
        $aula1 = Aula::factory()->create(['capacidad' => 30, 'habilitado' => true]);
        $aula2 = Aula::factory()->create(['capacidad' => 30, 'habilitado' => true]);
        $curso1 = Curso::factory()->create();
        $curso2 = Curso::factory()->create();

        // Horarios en diferentes aulas
        Horario::factory()->create([
            'curso_id' => $curso1->id,
            'aula_id' => $aula1->id,
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        Horario::factory()->create([
            'curso_id' => $curso2->id,
            'aula_id' => $aula2->id,
            'dia' => 'Lunes',
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
        ]);

        // Validar
        $result = $this->service->validateExistingSchedules();

        // Assertions
        $this->assertEmpty($result['conflicts']);
        $this->assertEquals(0, $result['statistics']['classroom_conflicts']);
        $this->assertEquals(0, $result['statistics']['professor_conflicts']);
    }

    /**
     * Test: Respeta el turno de mañana asignando solo horarios de mañana
     */
    public function test_respects_morning_shift(): void
    {
        // Crear turno de mañana
        $turnoManana = \App\Models\Turno::factory()->create([
            'nombre' => 'Mañana',
            'hora_inicio' => '08:00',
            'hora_fin' => '13:00',
            'habilitado' => true,
        ]);

        // Crear aula y materia
        $aula = Aula::factory()->create(['capacidad' => 30, 'habilitado' => true]);
        $materia = Materia::factory()->create(['horas_semanales' => 3]);

        // Crear curso con turno de mañana
        $curso = Curso::factory()->create([
            'materia_id' => $materia->id,
            'turno_id' => $turnoManana->id,
            'cupo_actual' => 20,
        ]);

        // Generar horarios
        $result = $this->service->generateSchedules(collect([$curso]));

        // Assertions
        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['schedules']);

        // Verificar que todos los horarios están en turno de mañana (antes de 14:00)
        foreach ($result['schedules'] as $schedule) {
            $horaInicio = \Carbon\Carbon::parse($schedule['hora_inicio']);
            $this->assertLessThan(14, $horaInicio->hour, 'El horario debe estar en turno de mañana');
        }
    }

    /**
     * Test: Respeta el turno de tarde asignando solo horarios de tarde
     */
    public function test_respects_afternoon_shift(): void
    {
        // Crear turno de tarde
        $turnoTarde = \App\Models\Turno::factory()->create([
            'nombre' => 'Tarde',
            'hora_inicio' => '14:00',
            'hora_fin' => '19:00',
            'habilitado' => true,
        ]);

        // Crear aula y materia
        $aula = Aula::factory()->create(['capacidad' => 30, 'habilitado' => true]);
        $materia = Materia::factory()->create(['horas_semanales' => 3]);

        // Crear curso con turno de tarde
        $curso = Curso::factory()->create([
            'materia_id' => $materia->id,
            'turno_id' => $turnoTarde->id,
            'cupo_actual' => 20,
        ]);

        // Generar horarios
        $result = $this->service->generateSchedules(collect([$curso]));

        // Assertions
        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['schedules']);

        // Verificar que todos los horarios están en turno de tarde (14:00 o después)
        foreach ($result['schedules'] as $schedule) {
            $horaInicio = \Carbon\Carbon::parse($schedule['hora_inicio']);
            $this->assertGreaterThanOrEqual(14, $horaInicio->hour, 'El horario debe estar en turno de tarde');
        }
    }

    /**
     * Test: Cursos de diferentes turnos no interfieren entre sí
     */
    public function test_different_shifts_do_not_interfere(): void
    {
        // Crear turnos
        $turnoManana = \App\Models\Turno::factory()->create([
            'nombre' => 'Mañana',
            'hora_inicio' => '08:00',
            'hora_fin' => '13:00',
            'habilitado' => true,
        ]);

        $turnoTarde = \App\Models\Turno::factory()->create([
            'nombre' => 'Tarde',
            'hora_inicio' => '14:00',
            'hora_fin' => '19:00',
            'habilitado' => true,
        ]);

        // Crear aulas
        Aula::factory()->count(2)->create(['capacidad' => 30, 'habilitado' => true]);

        // Crear materias
        $materia1 = Materia::factory()->create(['horas_semanales' => 4]);
        $materia2 = Materia::factory()->create(['horas_semanales' => 4]);

        // Crear cursos en diferentes turnos
        $cursoManana = Curso::factory()->create([
            'materia_id' => $materia1->id,
            'turno_id' => $turnoManana->id,
            'cupo_actual' => 20,
        ]);

        $cursoTarde = Curso::factory()->create([
            'materia_id' => $materia2->id,
            'turno_id' => $turnoTarde->id,
            'cupo_actual' => 20,
        ]);

        // Generar horarios para ambos cursos
        $result = $this->service->generateSchedules(collect([$cursoManana, $cursoTarde]));

        // Assertions
        $this->assertTrue($result['success']);
        $this->assertCount(8, $result['schedules']); // 4 + 4 horas

        // Verificar separación de turnos
        $schedulesByTurno = collect($result['schedules'])->groupBy(function ($schedule) {
            $hora = \Carbon\Carbon::parse($schedule['hora_inicio'])->hour;

            return $hora < 14 ? 'manana' : 'tarde';
        });

        $this->assertCount(4, $schedulesByTurno['manana'] ?? []);
        $this->assertCount(4, $schedulesByTurno['tarde'] ?? []);
    }
}
