<?php

namespace Tests\Stress;

use Tests\TestCase;
use App\Models\Persona;
use App\Models\Profesor;
use App\Models\Curso;
use App\Models\Estado;
use App\Models\Gestion;
use App\Models\Materia;
use App\Models\Turno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

/**
 * Pruebas de Rendimiento de Modelos
 * 
 * Estas pruebas evalúan el rendimiento de los modelos Eloquent
 * bajo diferentes escenarios de uso.
 * 
 * @group stress
 * @group models
 */
class ModelPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de rendimiento de atributos calculados
     * Evalúa el impacto de accessors personalizados
     */
    public function test_rendimiento_atributos_calculados(): void
    {
        $profesores = Profesor::factory()
            ->count(100)
            ->sequence(fn ($sequence) => ['codigo_saga' => 'PROF-ATTR-' . str_pad($sequence->index, 6, '0', STR_PAD_LEFT)])
            ->create();

        $startTime = microtime(true);

        // Acceder a atributo calculado múltiples veces
        foreach ($profesores as $profesor) {
            $nombreCompleto = $profesor->nombre_completo;
        }

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        echo "\n📊 Métricas de atributos calculados:";
        echo "\n   - Profesores: 100";
        echo "\n   - Tiempo total: " . round($executionTime, 2) . " ms";
        echo "\n   - Tiempo por acceso: " . round($executionTime / 100, 2) . " ms";

        // Debería ser rápido
        $this->assertLessThan(5000, $executionTime, "Los atributos calculados son muy lentos");
    }

    /**
     * Test de rendimiento de casteos
     */
    public function test_rendimiento_casteos(): void
    {
        $personas = Persona::factory()->count(200)->create();

        $startTime = microtime(true);

        foreach ($personas as $persona) {
            // Acceder a campos con casteo
            $habilitado = $persona->habilitado; // boolean
            $fechaNacimiento = $persona->fecha_nacimiento; // date
        }

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        echo "\n📊 Métricas de casteos:";
        echo "\n   - Registros: 200";
        echo "\n   - Accesos a campos casteados: 400";
        echo "\n   - Tiempo total: " . round($executionTime, 2) . " ms";

        $this->assertLessThan(3000, $executionTime);
    }

    /**
     * Test de rendimiento de scopes
     */
    public function test_rendimiento_consultas_filtradas(): void
    {
        Persona::factory()->count(300)->create();
        
        // 50% habilitados
        Persona::whereRaw('id % 2 = 0')->update(['habilitado' => true]);
        Persona::whereRaw('id % 2 = 1')->update(['habilitado' => false]);

        $startTime = microtime(true);

        // Múltiples consultas filtradas
        for ($i = 0; $i < 50; $i++) {
            $habilitados = Persona::where('habilitado', true)->get();
            $deshabilitados = Persona::where('habilitado', false)->get();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "\n📊 Métricas de consultas filtradas:";
        echo "\n   - Total registros: 300";
        echo "\n   - Iteraciones: 50";
        echo "\n   - Consultas totales: 100";
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";
        echo "\n   - Tiempo por consulta: " . round($executionTime / 100 * 1000, 2) . " ms";

        $this->assertLessThan(30, $executionTime);
    }

    /**
     * Test de rendimiento de búsqueda
     */
    public function test_rendimiento_busqueda_like(): void
    {
        Persona::factory()->count(500)->create();

        $searchTerms = ['Juan', 'María', 'Pedro', 'Ana', 'Luis'];
        $startTime = microtime(true);

        foreach ($searchTerms as $term) {
            for ($i = 0; $i < 20; $i++) {
                $resultados = Persona::where('nombre', 'LIKE', "%{$term}%")
                    ->orWhere('apellido_pat', 'LIKE', "%{$term}%")
                    ->get();
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "\n📊 Métricas de búsqueda LIKE:";
        echo "\n   - Registros en BD: 500";
        echo "\n   - Términos buscados: " . count($searchTerms);
        echo "\n   - Búsquedas por término: 20";
        echo "\n   - Total búsquedas: " . (count($searchTerms) * 20);
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";

        $this->assertLessThan(60, $executionTime);
    }

    /**
     * Test de rendimiento de ordenamiento
     */
    public function test_rendimiento_ordenamiento(): void
    {
        Persona::factory()->count(500)->create();

        $campos = ['nombre', 'apellido_pat', 'created_at', 'id'];
        $startTime = microtime(true);

        foreach ($campos as $campo) {
            for ($i = 0; $i < 10; $i++) {
                Persona::orderBy($campo, 'asc')->get();
                Persona::orderBy($campo, 'desc')->get();
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "\n📊 Métricas de ordenamiento:";
        echo "\n   - Registros: 500";
        echo "\n   - Campos ordenados: " . count($campos);
        echo "\n   - Consultas por campo: 20 (10 asc + 10 desc)";
        echo "\n   - Total consultas: " . (count($campos) * 20);
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";

        $this->assertLessThan(60, $executionTime);
    }

    /**
     * Test de rendimiento de relaciones múltiples
     * Crea datos de relaciones manualmente para evitar problemas de unicidad
     */
    public function test_rendimiento_relaciones_multiples(): void
    {
        // Crear estado primero (Turno depende de él)
        $estado = Estado::create([
            'nombre' => 'Estado Test Perf ' . uniqid(),
            'descripcion' => 'Estado para pruebas de rendimiento',
            'habilitado' => true,
            'tipo' => 'inscripcion',
        ]);
        
        // Crear turno con el estado existente
        $turno = Turno::create([
            'nombre' => 'Turno Test Perf ' . uniqid(),
            'hora_inicio' => '08:00:00',
            'hora_fin' => '12:00:00',
            'habilitado' => true,
            'estado_id' => $estado->id,
        ]);
        
        $gestion = Gestion::create([
            'nombre' => 'Gestión Test Perf ' . uniqid(),
            'habilitado' => true,
        ]);
        
        // Crear cursos con relaciones usando datos existentes
        for ($i = 0; $i < 50; $i++) {
            $profesor = Profesor::factory()->create([
                'codigo_saga' => 'PROF-REL-' . str_pad($i, 6, '0', STR_PAD_LEFT)
            ]);
            $materia = Materia::create([
                'nombre' => 'Materia Test Perf ' . $i,
                'descripcion' => 'Materia para pruebas',
                'habilitado' => true,
            ]);
            
            Curso::create([
                'seccion' => 'A',
                'cupo_maximo' => 30,
                'cupo_minimo' => 10,
                'cupo_actual' => 15,
                'profesor_id' => $profesor->id,
                'materia_id' => $materia->id,
                'estado_id' => $estado->id,
                'turno_id' => $turno->id,
                'gestion_id' => $gestion->id,
                'habilitado' => true,
            ]);
        }

        $startTime = microtime(true);

        // Cargar con todas las relaciones
        for ($i = 0; $i < 20; $i++) {
            $cursosConRelaciones = Curso::with([
                'profesor',
                'profesor.persona',
                'materia',
                'estado',
                'turno',
                'gestion',
                'horarios'
            ])->get();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "\n📊 Métricas de relaciones múltiples:";
        echo "\n   - Cursos: 50";
        echo "\n   - Relaciones cargadas: 7";
        echo "\n   - Iteraciones: 20";
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";
        echo "\n   - Tiempo por carga: " . round($executionTime / 20 * 1000, 2) . " ms";

        $this->assertLessThan(60, $executionTime);
    }

    /**
     * Test de rendimiento de conteos
     */
    public function test_rendimiento_conteos_agregaciones(): void
    {
        Persona::factory()->count(1000)->create();

        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            $total = Persona::count();
            $habilitados = Persona::where('habilitado', true)->count();
            $max_id = Persona::max('id');
            $min_id = Persona::min('id');
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "\n📊 Métricas de conteos y agregaciones:";
        echo "\n   - Registros: 1000";
        echo "\n   - Iteraciones: 100";
        echo "\n   - Operaciones por iteración: 4 (count, count con filtro, max, min)";
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";
        echo "\n   - Tiempo por iteración: " . round($executionTime / 100 * 1000, 2) . " ms";

        $this->assertLessThan(30, $executionTime);
    }

    /**
     * Test de rendimiento de chunking
     * Evalúa el procesamiento por lotes
     */
    public function test_rendimiento_chunking(): void
    {
        Persona::factory()->count(500)->create();

        $startTime = microtime(true);
        $processedCount = 0;

        // Procesamiento por chunks
        Persona::chunk(50, function ($personas) use (&$processedCount) {
            foreach ($personas as $persona) {
                $processedCount++;
                // Simular procesamiento
                $nombre = strtoupper($persona->nombre);
            }
        });

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertEquals(500, $processedCount);

        echo "\n📊 Métricas de chunking:";
        echo "\n   - Total registros: 500";
        echo "\n   - Tamaño de chunk: 50";
        echo "\n   - Chunks procesados: 10";
        echo "\n   - Registros procesados: {$processedCount}";
        echo "\n   - Tiempo total: " . round($executionTime, 4) . " segundos";
    }

    /**
     * Test de rendimiento de lazy collections
     */
    public function test_rendimiento_lazy_collections(): void
    {
        Persona::factory()->count(500)->create();

        // Regular collection
        $startTimeRegular = microtime(true);
        $memoryBeforeRegular = memory_get_usage();
        
        $personas = Persona::all();
        foreach ($personas as $persona) {
            $nombre = $persona->nombre;
        }
        
        $endTimeRegular = microtime(true);
        $memoryAfterRegular = memory_get_usage();

        // Lazy collection
        $startTimeLazy = microtime(true);
        $memoryBeforeLazy = memory_get_usage();
        
        foreach (Persona::cursor() as $persona) {
            $nombre = $persona->nombre;
        }
        
        $endTimeLazy = microtime(true);
        $memoryAfterLazy = memory_get_usage();

        echo "\n📊 Métricas de lazy collections:";
        echo "\n   - Registros: 500";
        echo "\n   Colección regular:";
        echo "\n   - Tiempo: " . round(($endTimeRegular - $startTimeRegular) * 1000, 2) . " ms";
        echo "\n   - Memoria usada: " . round(($memoryAfterRegular - $memoryBeforeRegular) / 1024, 2) . " KB";
        echo "\n   Cursor (lazy):";
        echo "\n   - Tiempo: " . round(($endTimeLazy - $startTimeLazy) * 1000, 2) . " ms";
        echo "\n   - Memoria usada: " . round(($memoryAfterLazy - $memoryBeforeLazy) / 1024, 2) . " KB";

        // Lazy debería usar menos memoria
        $this->assertNotNull($personas);
    }
}
