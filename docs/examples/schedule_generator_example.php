<?php

/**
 * Ejemplo de uso del Generador de Horarios
 * 
 * Este archivo muestra cómo utilizar el ScheduleGeneratorService
 * para generar horarios académicos.
 * 
 * NOTA: Este es un ejemplo de código. No está diseñado para ejecutarse
 * directamente, sino para mostrar cómo usar el servicio.
 */

use App\Services\ScheduleGeneratorService;
use App\Models\Curso;

// Inicializar el servicio
$scheduleGenerator = new ScheduleGeneratorService();

echo "═══════════════════════════════════════════════════════\n";
echo "  Ejemplo de Uso: Generador de Horarios\n";
echo "═══════════════════════════════════════════════════════\n\n";

// Ejemplo 1: Generar horarios para cursos de una gestión
echo "📋 Ejemplo 1: Generar horarios para una gestión\n";
echo "─────────────────────────────────────────────────\n";

$cursos = Curso::with(['materia', 'profesor'])
    ->where('gestion_id', 2024)
    ->where('habilitado', true)
    ->get();

echo "Cursos encontrados: {$cursos->count()}\n";

$result = $scheduleGenerator->generateSchedules($cursos);

if ($result['success']) {
    echo "✅ Generación exitosa!\n";
    echo "📊 Total de horarios generados: " . count($result['schedules']) . "\n";
    
    // Aplicar horarios
    if ($scheduleGenerator->applySchedules($result['schedules'], true)) {
        echo "✅ Horarios guardados exitosamente!\n";
    }
} else {
    echo "❌ Conflictos encontrados:\n";
    foreach ($result['conflicts'] as $conflict) {
        echo "  • {$conflict}\n";
    }
}

echo "\n";

// Ejemplo 2: Validar horarios existentes
echo "📋 Ejemplo 2: Validar horarios existentes\n";
echo "─────────────────────────────────────────────────\n";

$validation = $scheduleGenerator->validateExistingSchedules(2024);

echo "📈 Estadísticas:\n";
echo "  • Total de horarios: {$validation['statistics']['total_schedules']}\n";
echo "  • Conflictos de profesor: {$validation['statistics']['professor_conflicts']}\n";
echo "  • Conflictos de aula: {$validation['statistics']['classroom_conflicts']}\n";

if (!empty($validation['conflicts'])) {
    echo "\n⚠️ Conflictos detectados:\n";
    foreach ($validation['conflicts'] as $conflict) {
        echo "  • [{$conflict['type']}] {$conflict['message']}\n";
    }
} else {
    echo "\n✅ No se encontraron conflictos\n";
}

echo "\n═══════════════════════════════════════════════════════\n";
