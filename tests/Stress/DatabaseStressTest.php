<?php

namespace Tests\Stress;

use Tests\TestCase;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Estado;
use App\Models\Gestion;
use App\Models\Materia;
use App\Models\Persona;
use App\Models\Profesor;
use App\Models\Turno;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * Pruebas de Estrés para Base de Datos
 * 
 * Estas pruebas evalúan el rendimiento del sistema bajo condiciones de alta carga
 * en operaciones de base de datos como inserciones masivas, consultas complejas
 * y relaciones entre modelos.
 * 
 * @group stress
 */
class DatabaseStressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Número de registros para pruebas de carga masiva
     */
    private const BULK_INSERT_COUNT = 100;
    
    /**
     * Número de registros para pruebas de carga alta
     */
    private const HIGH_LOAD_COUNT = 500;

    /**
     * Tiempo máximo aceptable en segundos para operaciones
     */
    private const MAX_ACCEPTABLE_TIME = 30;

    /**
     * Test de inserción masiva de personas
     * Evalúa el rendimiento al crear múltiples registros de personas
     */
    public function test_insercion_masiva_personas(): void
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $personas = Persona::factory()->count(self::BULK_INSERT_COUNT)->create();

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = $endTime - $startTime;
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // MB

        // Verificar que se crearon todos los registros
        $this->assertCount(self::BULK_INSERT_COUNT, $personas);
        $this->assertEquals(self::BULK_INSERT_COUNT, Persona::count());

        // Verificar rendimiento
        $this->assertLessThan(
            self::MAX_ACCEPTABLE_TIME,
            $executionTime,
            "La inserción masiva de " . self::BULK_INSERT_COUNT . " personas tardó {$executionTime} segundos"
        );

        // Log de métricas
        $this->addToAssertionCount(1);
        echo "\n📊 Métricas de inserción masiva de personas:";
        echo "\n   - Registros creados: " . self::BULK_INSERT_COUNT;
        echo "\n   - Tiempo de ejecución: " . round($executionTime, 4) . " segundos";
        echo "\n   - Memoria utilizada: " . round($memoryUsed, 2) . " MB";
        echo "\n   - Registros por segundo: " . round(self::BULK_INSERT_COUNT / $executionTime, 2);
    }

    /**
     * Test de inserción masiva de profesores con relaciones
     * Evalúa el rendimiento al crear profesores que dependen de personas
     * Usa secuencia para evitar colisiones de codigo_saga único
     */
    public function test_insercion_masiva_profesores_con_relaciones(): void
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Usar sequence para generar códigos únicos
        $profesores = Profesor::factory()
            ->count(self::BULK_INSERT_COUNT)
            ->sequence(fn ($sequence) => ['codigo_saga' => 'PROF-STRESS-' . str_pad($sequence->index, 6, '0', STR_PAD_LEFT)])
            ->create();

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = $endTime - $startTime;
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024;

        // Verificar relaciones
        foreach ($profesores->take(10) as $profesor) {
            $this->assertNotNull($profesor->persona);
            $this->assertInstanceOf(Persona::class, $profesor->persona);
        }

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_TIME,
            $executionTime,
            "La inserción masiva de profesores tardó demasiado"
        );

        echo "\n📊 Métricas de inserción masiva de profesores:";
        echo "\n   - Profesores creados: " . self::BULK_INSERT_COUNT;
        echo "\n   - Personas creadas (automático): " . Persona::count();
        echo "\n   - Tiempo de ejecución: " . round($executionTime, 4) . " segundos";
        echo "\n   - Memoria utilizada: " . round($memoryUsed, 2) . " MB";
    }

    /**
     * Test de consultas complejas bajo carga
     * Evalúa el rendimiento de consultas con múltiples JOINs y filtros
     */
    public function test_consultas_complejas_bajo_carga(): void
    {
        // Crear datos de prueba con códigos únicos
        $profesores = Profesor::factory()
            ->count(50)
            ->sequence(fn ($sequence) => ['codigo_saga' => 'PROF-QUERY-' . str_pad($sequence->index, 6, '0', STR_PAD_LEFT)])
            ->create();
        
        $startTime = microtime(true);

        // Consulta compleja con eager loading
        for ($i = 0; $i < 100; $i++) {
            $resultado = Profesor::with(['persona', 'documentos', 'cursos'])
                ->where('habilitado', true)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_TIME,
            $executionTime,
            "Las consultas complejas tardaron demasiado"
        );

        echo "\n📊 Métricas de consultas complejas:";
        echo "\n   - Iteraciones: 100";
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";
        echo "\n   - Tiempo promedio por consulta: " . round($executionTime / 100 * 1000, 2) . " ms";
    }

    /**
     * Test de transacciones concurrentes
     * Simula múltiples operaciones en transacciones
     */
    public function test_transacciones_concurrentes(): void
    {
        $startTime = microtime(true);
        $successCount = 0;
        $failCount = 0;

        for ($i = 0; $i < 50; $i++) {
            try {
                DB::transaction(function () use ($i) {
                    $persona = Persona::factory()->create();
                    $profesor = Profesor::factory()->create([
                        'persona_id' => $persona->id,
                        'codigo_saga' => 'PROF-TX-' . str_pad($i, 6, '0', STR_PAD_LEFT) . '-' . uniqid()
                    ]);
                    
                    // Simular operación adicional
                    $profesor->update(['habilitado' => false]);
                    $profesor->update(['habilitado' => true]);
                });
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertEquals(50, $successCount, "Algunas transacciones fallaron");
        $this->assertEquals(0, $failCount);

        echo "\n📊 Métricas de transacciones concurrentes:";
        echo "\n   - Transacciones exitosas: {$successCount}";
        echo "\n   - Transacciones fallidas: {$failCount}";
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";
    }

    /**
     * Test de actualización masiva
     * Evalúa el rendimiento de actualizaciones en lote
     */
    public function test_actualizacion_masiva(): void
    {
        // Crear datos iniciales
        $personas = Persona::factory()->count(self::BULK_INSERT_COUNT)->create([
            'habilitado' => false
        ]);

        $startTime = microtime(true);

        // Actualización masiva
        Persona::where('habilitado', false)->update(['habilitado' => true]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar actualización
        $this->assertEquals(self::BULK_INSERT_COUNT, Persona::where('habilitado', true)->count());

        $this->assertLessThan(
            5, // Actualizaciones masivas deben ser rápidas
            $executionTime,
            "La actualización masiva tardó demasiado"
        );

        echo "\n📊 Métricas de actualización masiva:";
        echo "\n   - Registros actualizados: " . self::BULK_INSERT_COUNT;
        echo "\n   - Tiempo de ejecución: " . round($executionTime, 4) . " segundos";
    }

    /**
     * Test de eliminación masiva con SoftDeletes
     * Evalúa el rendimiento del borrado lógico en masa
     */
    public function test_eliminacion_masiva_soft_delete(): void
    {
        // Crear datos con códigos únicos
        $profesores = Profesor::factory()
            ->count(self::BULK_INSERT_COUNT)
            ->sequence(fn ($sequence) => ['codigo_saga' => 'PROF-DEL-' . str_pad($sequence->index, 6, '0', STR_PAD_LEFT)])
            ->create();

        $startTime = microtime(true);

        // Soft delete masivo
        Profesor::whereNotNull('id')->delete();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verificar soft delete
        $this->assertEquals(0, Profesor::count());
        $this->assertEquals(self::BULK_INSERT_COUNT, Profesor::withTrashed()->count());

        echo "\n📊 Métricas de eliminación masiva (soft delete):";
        echo "\n   - Registros eliminados: " . self::BULK_INSERT_COUNT;
        echo "\n   - Tiempo de ejecución: " . round($executionTime, 4) . " segundos";
    }

    /**
     * Test de carga de relaciones N+1
     * Verifica que no haya problemas de N+1 queries
     */
    public function test_prevencion_n_plus_1_queries(): void
    {
        // Crear datos con relaciones usando códigos únicos
        Profesor::factory()
            ->count(20)
            ->sequence(fn ($sequence) => ['codigo_saga' => 'PROF-N1-' . str_pad($sequence->index, 6, '0', STR_PAD_LEFT)])
            ->create();

        DB::enableQueryLog();

        // Sin eager loading (potencial N+1)
        $profesoresSinEager = Profesor::all();
        foreach ($profesoresSinEager as $prof) {
            $nombre = $prof->persona->nombre ?? 'Sin nombre';
        }
        $queriesSinEager = count(DB::getQueryLog());

        DB::flushQueryLog();

        // Con eager loading (optimizado)
        $profesoresConEager = Profesor::with('persona')->get();
        foreach ($profesoresConEager as $prof) {
            $nombre = $prof->persona->nombre ?? 'Sin nombre';
        }
        $queriesConEager = count(DB::getQueryLog());

        DB::disableQueryLog();

        // El eager loading debe generar menos queries
        $this->assertLessThan(
            $queriesSinEager,
            $queriesConEager,
            "El eager loading debe reducir el número de queries"
        );

        echo "\n📊 Métricas de prevención N+1:";
        echo "\n   - Queries sin eager loading: {$queriesSinEager}";
        echo "\n   - Queries con eager loading: {$queriesConEager}";
        echo "\n   - Queries evitadas: " . ($queriesSinEager - $queriesConEager);
    }

    /**
     * Test de paginación bajo carga
     * Evalúa el rendimiento de la paginación con grandes conjuntos de datos
     */
    public function test_paginacion_bajo_carga(): void
    {
        // Crear muchos registros
        Persona::factory()->count(self::HIGH_LOAD_COUNT)->create();

        $startTime = microtime(true);

        // Simular navegación por páginas
        for ($page = 1; $page <= 10; $page++) {
            $resultado = Persona::orderBy('id')
                ->paginate(50, ['*'], 'page', $page);
            
            $this->assertNotEmpty($resultado->items());
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_TIME,
            $executionTime,
            "La paginación tardó demasiado"
        );

        echo "\n📊 Métricas de paginación:";
        echo "\n   - Total de registros: " . self::HIGH_LOAD_COUNT;
        echo "\n   - Páginas navegadas: 10";
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";
        echo "\n   - Tiempo por página: " . round($executionTime / 10 * 1000, 2) . " ms";
    }
}
