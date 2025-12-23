<?php

namespace App\Console\Commands;

use App\Models\Curso;
use App\Services\ScheduleGeneratorService;
use Illuminate\Console\Command;

class GenerateSchedulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:generate
                            {--gestion= : ID de la gestión para filtrar cursos}
                            {--curso=* : IDs específicos de cursos a procesar}
                            {--apply : Aplicar los horarios generados a la base de datos}
                            {--validate : Solo validar horarios existentes sin generar nuevos}
                            {--reorganize : Reorganizar horarios existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera, valida o reorganiza horarios académicos para cursos';

    private ScheduleGeneratorService $scheduleGenerator;

    /**
     * Create a new command instance.
     */
    public function __construct(ScheduleGeneratorService $scheduleGenerator)
    {
        parent::__construct();
        $this->scheduleGenerator = $scheduleGenerator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════');
        $this->info('   Generador de Horarios - Sistema Montesori');
        $this->info('═══════════════════════════════════════════════════');
        $this->newLine();

        // Modo validación
        if ($this->option('validate')) {
            return $this->handleValidation();
        }

        // Modo reorganización
        if ($this->option('reorganize')) {
            return $this->handleReorganize();
        }

        // Modo generación
        return $this->handleGeneration();
    }

    /**
     * Maneja la validación de horarios existentes
     */
    private function handleValidation(): int
    {
        $this->info('🔍 Validando horarios existentes...');
        $this->newLine();

        $gestionId = $this->option('gestion');
        $result = $this->scheduleGenerator->validateExistingSchedules($gestionId);

        $this->displayStatistics($result['statistics']);
        $this->newLine();

        if (empty($result['conflicts'])) {
            $this->info('✅ No se encontraron conflictos en los horarios.');

            return Command::SUCCESS;
        }

        $this->warn('⚠️  Se encontraron los siguientes conflictos:');
        $this->newLine();

        foreach ($result['conflicts'] as $conflict) {
            $icon = $conflict['type'] === 'professor' ? '👨‍🏫' : '🏫';
            $this->line("{$icon} {$conflict['message']}");
        }

        return Command::FAILURE;
    }

    /**
     * Maneja la reorganización de horarios
     */
    private function handleReorganize(): int
    {
        $cursoIds = $this->option('curso');

        if (empty($cursoIds)) {
            $this->error('❌ Debe especificar al menos un curso para reorganizar (--curso=ID)');

            return Command::FAILURE;
        }

        $this->info('🔄 Reorganizando horarios...');
        $this->newLine();

        $result = $this->scheduleGenerator->reorganizeSchedules($cursoIds);

        return $this->displayAndApplyResults($result);
    }

    /**
     * Maneja la generación de nuevos horarios
     */
    private function handleGeneration(): int
    {
        $this->info('📅 Generando horarios...');
        $this->newLine();

        // Obtener cursos
        $cursos = $this->getCursos();

        if ($cursos->isEmpty()) {
            $this->error('❌ No se encontraron cursos para procesar.');

            return Command::FAILURE;
        }

        $this->info("📚 Procesando {$cursos->count()} curso(s)...");
        $this->newLine();

        // Generar horarios
        $result = $this->scheduleGenerator->generateSchedules($cursos);

        return $this->displayAndApplyResults($result);
    }

    /**
     * Obtiene los cursos a procesar según las opciones
     */
    private function getCursos()
    {
        $query = Curso::with(['materia', 'profesor']);

        // Filtrar por IDs específicos
        if ($cursoIds = $this->option('curso')) {
            $query->whereIn('id', $cursoIds);
        }

        // Filtrar por gestión
        if ($gestionId = $this->option('gestion')) {
            $query->where('gestion_id', $gestionId);
        }

        return $query->where('habilitado', true)->get();
    }

    /**
     * Muestra los resultados y opcionalmente los aplica
     */
    private function displayAndApplyResults(array $result): int
    {
        if (! $result['success'] || ! empty($result['conflicts'])) {
            $this->warn('⚠️  Se encontraron conflictos durante la generación:');
            $this->newLine();

            foreach ($result['conflicts'] as $conflict) {
                $this->line("  • {$conflict}");
            }
            $this->newLine();
        }

        $totalSchedules = count($result['schedules']);

        if ($totalSchedules === 0) {
            $this->error('❌ No se pudieron generar horarios.');

            return Command::FAILURE;
        }

        $this->info("✅ Se generaron {$totalSchedules} asignación(es) de horario.");
        $this->newLine();

        // Mostrar muestra de los horarios generados
        $this->displayScheduleSample($result['schedules']);

        // Aplicar si se especificó la opción
        if ($this->option('apply')) {
            if ($this->confirm('¿Desea aplicar estos horarios a la base de datos?', true)) {
                $applied = $this->scheduleGenerator->applySchedules($result['schedules'], true);

                if ($applied) {
                    $this->info('✅ Horarios aplicados exitosamente a la base de datos.');

                    return Command::SUCCESS;
                } else {
                    $this->error('❌ Error al aplicar los horarios a la base de datos.');

                    return Command::FAILURE;
                }
            }
        } else {
            $this->comment('💡 Use la opción --apply para guardar los horarios en la base de datos.');
        }

        return Command::SUCCESS;
    }

    /**
     * Muestra una muestra de los horarios generados
     */
    private function displayScheduleSample(array $schedules): void
    {
        $this->info('📋 Muestra de horarios generados (primeros 10):');
        $this->newLine();

        $headers = ['Curso ID', 'Aula ID', 'Día', 'Inicio', 'Fin'];
        $rows = [];

        foreach (array_slice($schedules, 0, 10) as $schedule) {
            $rows[] = [
                $schedule['curso_id'],
                $schedule['aula_id'],
                $schedule['dia'],
                $schedule['hora_inicio'],
                $schedule['hora_fin'],
            ];
        }

        $this->table($headers, $rows);

        if (count($schedules) > 10) {
            $remaining = count($schedules) - 10;
            $this->comment("  ... y {$remaining} más.");
        }

        $this->newLine();
    }

    /**
     * Muestra estadísticas de validación
     */
    private function displayStatistics(array $statistics): void
    {
        $this->info('📊 Estadísticas:');
        $this->line("  • Total de horarios: {$statistics['total_schedules']}");
        $this->line("  • Conflictos de profesor: {$statistics['professor_conflicts']}");
        $this->line("  • Conflictos de aula: {$statistics['classroom_conflicts']}");
    }
}
