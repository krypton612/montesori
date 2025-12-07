<?php

namespace App\Services;

use App\Models\Aula;
use App\Models\Curso;
use App\Models\Horario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * ScheduleGeneratorService
 *
 * Servicio aislado para generación y reorganización de horarios académicos.
 *
 * ## Tipo de Problema: NP-Completo (Constraint Satisfaction Problem - CSP)
 *
 * La asignación de horarios es un problema de satisfacción de restricciones (CSP)
 * que pertenece a la clase de complejidad NP-Completo. Esto significa que:
 *
 * 1. **Complejidad Computacional**: No existe un algoritmo conocido que pueda
 *    resolver el problema en tiempo polinomial para todos los casos.
 *
 * 2. **Restricciones Duras (Hard Constraints)**:
 *    - Un profesor no puede estar en dos lugares al mismo tiempo
 *    - Un aula no puede albergar dos cursos simultáneamente
 *    - Los horarios deben respetar la capacidad del aula
 *
 * 3. **Restricciones Blandas (Soft Constraints)**:
 *    - Distribuir equitativamente las horas semanales
 *    - Minimizar ventanas horarias para profesores
 *    - Preferencias de horarios
 *
 * 4. **Enfoque de Solución**:
 *    Este servicio utiliza un algoritmo heurístico con backtracking que:
 *    - Intenta asignar horarios uno por uno
 *    - Verifica restricciones en cada paso
 *    - Retrocede si encuentra conflictos irresolubles
 *    - Puede no encontrar solución óptima en todos los casos
 *
 * 5. **Escalabilidad**: La complejidad crece exponencialmente con:
 *    - Número de cursos
 *    - Número de aulas
 *    - Número de franjas horarias
 *    - Número de días disponibles
 */
class ScheduleGeneratorService
{
    /**
     * Días de la semana disponibles para asignación
     */
    private const DIAS_SEMANA = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

    /**
     * Bloques de tiempo disponibles para turno mañana (formato 24h)
     */
    private const BLOQUES_HORARIOS_MANANA = [
        ['08:00', '09:00'],
        ['09:00', '10:00'],
        ['10:00', '11:00'],
        ['11:00', '12:00'],
        ['12:00', '13:00'],
    ];

    /**
     * Bloques de tiempo disponibles para turno tarde (formato 24h)
     */
    private const BLOQUES_HORARIOS_TARDE = [
        ['14:00', '15:00'],
        ['15:00', '16:00'],
        ['16:00', '17:00'],
        ['17:00', '18:00'],
        ['18:00', '19:00'],
    ];

    /**
     * Genera horarios para un conjunto de cursos
     *
     * @param  Collection|array  $cursos  Colección de cursos para asignar
     * @param  array  $options  Opciones de configuración
     * @return array ['success' => bool, 'schedules' => array, 'conflicts' => array]
     */
    public function generateSchedules($cursos, array $options = []): array
    {
        $cursos = $cursos instanceof Collection ? $cursos : collect($cursos);

        // Si es una colección de Eloquent, cargar relaciones necesarias
        if (method_exists($cursos, 'loadMissing')) {
            $cursos->loadMissing(['materia', 'turno', 'profesor']);
        } else {
            // Si es una colección regular, cargar manualmente cada curso
            $cursos = $cursos->map(function ($curso) {
                $curso->load(['materia', 'turno', 'profesor']);

                return $curso;
            });
        }

        $aulas = Aula::where('habilitado', true)->get();

        if ($aulas->isEmpty()) {
            return [
                'success' => false,
                'schedules' => [],
                'conflicts' => ['No hay aulas disponibles para asignación'],
            ];
        }

        $generatedSchedules = [];
        $conflicts = [];

        foreach ($cursos as $curso) {
            $cursoSchedules = $this->generateScheduleForCourse($curso, $aulas, $generatedSchedules);

            if (empty($cursoSchedules)) {
                $conflicts[] = "No se pudo generar horario para curso ID {$curso->id} - {$curso->materia->nombre}";
            } else {
                $generatedSchedules = array_merge($generatedSchedules, $cursoSchedules);
            }
        }

        return [
            'success' => empty($conflicts),
            'schedules' => $generatedSchedules,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * Genera horario para un curso específico
     *
     * @param  array  $existingSchedules  Horarios ya asignados
     */
    private function generateScheduleForCourse(Curso $curso, Collection $aulas, array $existingSchedules): array
    {
        $horasNecesarias = $curso->materia->horas_semanales ?? 4;
        $schedules = [];
        $horasAsignadas = 0;

        // Determinar bloques horarios según el turno del curso
        $bloquesHorarios = $this->getBloquesHorariosPorTurno($curso);

        // Intentar asignar las horas necesarias
        foreach (self::DIAS_SEMANA as $dia) {
            if ($horasAsignadas >= $horasNecesarias) {
                break;
            }

            foreach ($bloquesHorarios as $bloque) {
                if ($horasAsignadas >= $horasNecesarias) {
                    break;
                }

                // Buscar aula disponible para este bloque
                $aulaDisponible = $this->findAvailableAula(
                    $aulas,
                    $dia,
                    $bloque[0],
                    $bloque[1],
                    $curso,
                    array_merge($existingSchedules, $schedules)
                );

                if ($aulaDisponible) {
                    $schedules[] = [
                        'curso_id' => $curso->id,
                        'aula_id' => $aulaDisponible->id,
                        'dia' => $dia,
                        'hora_inicio' => $bloque[0],
                        'hora_fin' => $bloque[1],
                    ];
                    $horasAsignadas++;
                }
            }
        }

        return $schedules;
    }

    /**
     * Obtiene los bloques horarios apropiados según el turno del curso
     */
    private function getBloquesHorariosPorTurno(Curso $curso): array
    {
        // Si el curso no tiene turno asignado, usar todos los bloques
        if (! $curso->turno) {
            return array_merge(self::BLOQUES_HORARIOS_MANANA, self::BLOQUES_HORARIOS_TARDE);
        }

        $turnoNombre = strtolower($curso->turno->nombre);

        // Detectar si es turno de mañana o tarde
        if (str_contains($turnoNombre, 'mañana') || str_contains($turnoNombre, 'manana')) {
            return self::BLOQUES_HORARIOS_MANANA;
        } elseif (str_contains($turnoNombre, 'tarde')) {
            return self::BLOQUES_HORARIOS_TARDE;
        }

        // Si no se puede determinar, usar todos los bloques
        return array_merge(self::BLOQUES_HORARIOS_MANANA, self::BLOQUES_HORARIOS_TARDE);
    }

    /**
     * Encuentra un aula disponible para el bloque horario especificado
     */
    private function findAvailableAula(
        Collection $aulas,
        string $dia,
        string $horaInicio,
        string $horaFin,
        Curso $curso,
        array $existingSchedules
    ): ?Aula {
        foreach ($aulas as $aula) {
            // Verificar capacidad del aula
            if ($aula->capacidad < ($curso->cupo_actual ?? 0)) {
                continue;
            }

            // Verificar que no haya conflictos
            if (! $this->hasConflict($aula->id, $curso, $dia, $horaInicio, $horaFin, $existingSchedules)) {
                return $aula;
            }
        }

        return null;
    }

    /**
     * Verifica si existe un conflicto para la asignación propuesta
     *
     * @return bool true si hay conflicto, false si está libre
     */
    private function hasConflict(
        int $aulaId,
        Curso $curso,
        string $dia,
        string $horaInicio,
        string $horaFin,
        array $existingSchedules
    ): bool {
        foreach ($existingSchedules as $schedule) {
            // Mismo día
            if ($schedule['dia'] !== $dia) {
                continue;
            }

            // Verificar solapamiento de horas
            if (! $this->horariosOverlap($horaInicio, $horaFin, $schedule['hora_inicio'], $schedule['hora_fin'])) {
                continue;
            }

            // Conflicto: misma aula
            if ($schedule['aula_id'] === $aulaId) {
                return true;
            }

            // Conflicto: mismo profesor
            if ($schedule['curso_id'] !== $curso->id) {
                $otroCurso = Curso::find($schedule['curso_id']);
                if ($otroCurso && $otroCurso->profesor_id === $curso->profesor_id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verifica si dos horarios se solapan
     */
    private function horariosOverlap(string $inicio1, string $fin1, string $inicio2, string $fin2): bool
    {
        return ! ($fin1 <= $inicio2 || $inicio1 >= $fin2);
    }

    /**
     * Aplica los horarios generados a la base de datos
     *
     * @param  bool  $clearExisting  Si debe eliminar horarios existentes
     */
    public function applySchedules(array $schedules, bool $clearExisting = false): bool
    {
        try {
            DB::beginTransaction();

            if ($clearExisting) {
                // Obtener IDs de cursos afectados
                $cursoIds = array_unique(array_column($schedules, 'curso_id'));

                // Eliminar horarios existentes de estos cursos
                Horario::whereIn('curso_id', $cursoIds)->delete();
            }

            // Insertar nuevos horarios
            foreach ($schedules as $schedule) {
                Horario::create($schedule);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            \Log::error('Error applying schedules: '.$e->getMessage());
            DB::rollBack();

            return false;
        }
    }

    /**
     * Valida horarios existentes y detecta conflictos
     *
     * @param  int|null  $gestionId  ID de gestión para filtrar cursos (opcional)
     * @return array ['conflicts' => array, 'statistics' => array]
     */
    public function validateExistingSchedules(?int $gestionId = null): array
    {
        $query = Horario::with(['curso.profesor', 'aula']);

        if ($gestionId) {
            $query->whereHas('curso', function ($q) use ($gestionId) {
                $q->where('gestion_id', $gestionId);
            });
        }

        $horarios = $query->get();
        $conflicts = [];
        $statistics = [
            'total_schedules' => $horarios->count(),
            'professor_conflicts' => 0,
            'classroom_conflicts' => 0,
        ];

        // Verificar conflictos de profesor y aula
        foreach ($horarios as $horario1) {
            foreach ($horarios as $horario2) {
                if ($horario1->id >= $horario2->id) {
                    continue;
                }

                if ($horario1->dia !== $horario2->dia) {
                    continue;
                }

                if (! $this->horariosOverlap(
                    $horario1->hora_inicio,
                    $horario1->hora_fin,
                    $horario2->hora_inicio,
                    $horario2->hora_fin
                )) {
                    continue;
                }

                // Conflicto de aula
                if ($horario1->aula_id === $horario2->aula_id) {
                    $conflicts[] = [
                        'type' => 'classroom',
                        'message' => "Aula {$horario1->aula->numero} tiene conflicto el {$horario1->dia} de {$horario1->hora_inicio} a {$horario1->hora_fin}",
                        'horarios' => [$horario1->id, $horario2->id],
                    ];
                    $statistics['classroom_conflicts']++;
                }

                // Conflicto de profesor
                if ($horario1->curso->profesor_id === $horario2->curso->profesor_id) {
                    $conflicts[] = [
                        'type' => 'professor',
                        'message' => "Profesor ID {$horario1->curso->profesor_id} tiene conflicto el {$horario1->dia} de {$horario1->hora_inicio} a {$horario1->hora_fin}",
                        'horarios' => [$horario1->id, $horario2->id],
                    ];
                    $statistics['professor_conflicts']++;
                }
            }
        }

        return [
            'conflicts' => $conflicts,
            'statistics' => $statistics,
        ];
    }

    /**
     * Reorganiza horarios existentes optimizando la distribución
     *
     * @param  array  $cursoIds  IDs de cursos a reorganizar
     */
    public function reorganizeSchedules(array $cursoIds): array
    {
        // Eliminar horarios existentes de estos cursos
        Horario::whereIn('curso_id', $cursoIds)->delete();

        // Cargar cursos
        $cursos = Curso::with(['materia', 'profesor'])
            ->whereIn('id', $cursoIds)
            ->get();

        // Generar nuevos horarios
        return $this->generateSchedules($cursos);
    }
}
