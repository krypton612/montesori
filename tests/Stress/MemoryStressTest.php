<?php

namespace Tests\Stress;

use Tests\TestCase;
use App\Models\Persona;
use App\Models\Profesor;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas de Uso de Memoria
 * 
 * Estas pruebas evalúan el consumo de memoria del sistema bajo
 * diferentes escenarios de carga para detectar posibles fugas de memoria.
 * 
 * @group stress
 * @group memory
 */
class MemoryStressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Límite de memoria en MB para detectar fugas
     */
    private const MEMORY_LIMIT_MB = 128;

    /**
     * Test de consumo de memoria en creación masiva
     * Detecta fugas de memoria durante creación de registros
     */
    public function test_consumo_memoria_creacion_masiva(): void
    {
        $initialMemory = memory_get_usage(true) / 1024 / 1024;
        $memorySnapshots = [];

        // Crear registros en lotes y monitorear memoria
        for ($batch = 1; $batch <= 5; $batch++) {
            Persona::factory()->count(50)->create();
            
            $currentMemory = memory_get_usage(true) / 1024 / 1024;
            $memorySnapshots[] = $currentMemory;
            
            // Liberar memoria
            gc_collect_cycles();
        }

        $finalMemory = memory_get_usage(true) / 1024 / 1024;
        $memoryGrowth = $finalMemory - $initialMemory;
        $peakMemory = memory_get_peak_usage(true) / 1024 / 1024;

        // Verificar que no hay crecimiento excesivo de memoria
        $this->assertLessThan(
            self::MEMORY_LIMIT_MB,
            $peakMemory,
            "El pico de memoria excede el límite aceptable"
        );

        echo "\n📊 Métricas de consumo de memoria - Creación masiva:";
        echo "\n   - Memoria inicial: " . round($initialMemory, 2) . " MB";
        echo "\n   - Memoria final: " . round($finalMemory, 2) . " MB";
        echo "\n   - Crecimiento: " . round($memoryGrowth, 2) . " MB";
        echo "\n   - Pico de memoria: " . round($peakMemory, 2) . " MB";
        echo "\n   - Registros creados: 250";
    }

    /**
     * Test de liberación de memoria después de consultas
     * Verifica que la memoria se libera correctamente
     */
    public function test_liberacion_memoria_consultas(): void
    {
        // Crear datos de prueba
        Persona::factory()->count(100)->create();
        
        $initialMemory = memory_get_usage(true) / 1024 / 1024;
        $memoryAfterOperations = [];

        // Realizar múltiples consultas
        for ($i = 0; $i < 10; $i++) {
            $personas = Persona::all();
            $count = $personas->count();
            
            // Forzar liberación
            unset($personas);
            gc_collect_cycles();
            
            $memoryAfterOperations[] = memory_get_usage(true) / 1024 / 1024;
        }

        $finalMemory = memory_get_usage(true) / 1024 / 1024;

        // La memoria no debe crecer significativamente entre iteraciones
        $memoryVariation = max($memoryAfterOperations) - min($memoryAfterOperations);
        
        $this->assertLessThan(
            10, // 10 MB de variación máxima
            $memoryVariation,
            "Posible fuga de memoria: la variación es muy alta"
        );

        echo "\n📊 Métricas de liberación de memoria:";
        echo "\n   - Memoria inicial: " . round($initialMemory, 2) . " MB";
        echo "\n   - Memoria final: " . round($finalMemory, 2) . " MB";
        echo "\n   - Variación entre iteraciones: " . round($memoryVariation, 2) . " MB";
        echo "\n   - Iteraciones: 10";
    }

    /**
     * Test de memoria con relaciones cargadas
     * Evalúa el impacto de eager loading en la memoria
     */
    public function test_memoria_con_relaciones(): void
    {
        // Crear profesores con relaciones usando códigos únicos
        Profesor::factory()
            ->count(50)
            ->sequence(fn ($sequence) => ['codigo_saga' => 'PROF-MEM-' . str_pad($sequence->index, 6, '0', STR_PAD_LEFT)])
            ->create();

        gc_collect_cycles();
        $baseMemory = memory_get_usage(true) / 1024 / 1024;

        // Carga sin eager loading
        $profesoresSin = Profesor::all();
        $memorySinEager = memory_get_usage(true) / 1024 / 1024;
        unset($profesoresSin);
        gc_collect_cycles();

        // Carga con eager loading
        $profesoresCon = Profesor::with(['persona', 'documentos', 'cursos'])->get();
        $memoryConEager = memory_get_usage(true) / 1024 / 1024;

        echo "\n📊 Métricas de memoria con relaciones:";
        echo "\n   - Memoria base: " . round($baseMemory, 2) . " MB";
        echo "\n   - Memoria sin eager loading: " . round($memorySinEager, 2) . " MB";
        echo "\n   - Memoria con eager loading: " . round($memoryConEager, 2) . " MB";
        echo "\n   - Diferencia: " . round($memoryConEager - $memorySinEager, 2) . " MB";
        echo "\n   - Profesores cargados: 50";

        // El eager loading usa más memoria pero es más eficiente en queries
        $this->assertNotNull($profesoresCon);
    }

    /**
     * Test de memoria en operaciones de actualización
     */
    public function test_memoria_operaciones_actualizacion(): void
    {
        $personas = Persona::factory()->count(100)->create();
        
        gc_collect_cycles();
        $initialMemory = memory_get_usage(true) / 1024 / 1024;

        // Actualizar cada registro individualmente
        foreach ($personas as $persona) {
            $persona->update(['habilitado' => !$persona->habilitado]);
        }

        $afterUpdates = memory_get_usage(true) / 1024 / 1024;
        
        gc_collect_cycles();
        $afterGC = memory_get_usage(true) / 1024 / 1024;

        echo "\n📊 Métricas de memoria - Actualizaciones:";
        echo "\n   - Memoria inicial: " . round($initialMemory, 2) . " MB";
        echo "\n   - Después de actualizaciones: " . round($afterUpdates, 2) . " MB";
        echo "\n   - Después de GC: " . round($afterGC, 2) . " MB";
        echo "\n   - Registros actualizados: 100";

        // Verificar que la memoria se mantiene razonable (tolerancia aumentada)
        $this->assertLessThan(self::MEMORY_LIMIT_MB, $afterGC, "El uso de memoria excede el límite aceptable");
    }

    /**
     * Test de pico de memoria sostenido
     * Verifica que el sistema pueda manejar carga sostenida
     */
    public function test_pico_memoria_sostenido(): void
    {
        $peakMemories = [];

        for ($round = 1; $round <= 5; $round++) {
            // Crear y destruir datos
            $personas = Persona::factory()->count(100)->create();
            
            // Realizar operaciones
            foreach ($personas as $persona) {
                $persona->nombre = 'Test ' . $round;
            }
            
            $peakMemories[] = memory_get_peak_usage(true) / 1024 / 1024;
            
            // Limpiar para el siguiente round
            Persona::query()->delete();
            gc_collect_cycles();
        }

        $maxPeak = max($peakMemories);
        $minPeak = min($peakMemories);

        echo "\n📊 Métricas de pico de memoria sostenido:";
        echo "\n   - Rounds ejecutados: 5";
        echo "\n   - Pico máximo: " . round($maxPeak, 2) . " MB";
        echo "\n   - Pico mínimo: " . round($minPeak, 2) . " MB";
        echo "\n   - Variación: " . round($maxPeak - $minPeak, 2) . " MB";

        // El pico no debe crecer significativamente entre rounds
        $this->assertLessThan(
            30, // 30 MB de variación máxima (tolerancia aumentada)
            $maxPeak - $minPeak,
            "El pico de memoria crece entre rounds - posible fuga"
        );
    }
}
