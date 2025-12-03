<?php

namespace Tests\Stress;

use Tests\TestCase;
use App\Models\Persona;
use App\Models\Profesor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Pruebas de Carga HTTP
 * 
 * Estas pruebas evalúan el rendimiento del sistema bajo múltiples
 * solicitudes HTTP concurrentes simuladas.
 * 
 * @group stress
 * @group http
 */
class HttpLoadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Número de solicitudes para pruebas de carga
     */
    private const REQUEST_COUNT = 100;

    /**
     * Tiempo máximo aceptable en segundos
     */
    private const MAX_ACCEPTABLE_TIME = 30;

    /**
     * Test de carga en la página principal
     * Simula múltiples solicitudes a la ruta raíz
     */
    public function test_carga_pagina_principal(): void
    {
        $startTime = microtime(true);
        $successCount = 0;
        $failCount = 0;
        $responseTimes = [];

        for ($i = 0; $i < self::REQUEST_COUNT; $i++) {
            $requestStart = microtime(true);
            
            $response = $this->get('/');
            
            $requestEnd = microtime(true);
            $responseTimes[] = ($requestEnd - $requestStart) * 1000; // ms

            if ($response->status() === 200) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;

        // Calcular estadísticas
        $avgResponseTime = array_sum($responseTimes) / count($responseTimes);
        $maxResponseTime = max($responseTimes);
        $minResponseTime = min($responseTimes);

        // Aserciones
        $this->assertEquals(self::REQUEST_COUNT, $successCount, "Algunas solicitudes fallaron");
        $this->assertLessThan(self::MAX_ACCEPTABLE_TIME, $totalTime);

        echo "\n📊 Métricas de carga - Página Principal:";
        echo "\n   - Total solicitudes: " . self::REQUEST_COUNT;
        echo "\n   - Exitosas: {$successCount}";
        echo "\n   - Fallidas: {$failCount}";
        echo "\n   - Tiempo total: " . round($totalTime, 4) . " segundos";
        echo "\n   - Solicitudes por segundo: " . round(self::REQUEST_COUNT / $totalTime, 2);
        echo "\n   - Tiempo respuesta promedio: " . round($avgResponseTime, 2) . " ms";
        echo "\n   - Tiempo respuesta mínimo: " . round($minResponseTime, 2) . " ms";
        echo "\n   - Tiempo respuesta máximo: " . round($maxResponseTime, 2) . " ms";
    }

    /**
     * Test de carga en panel de administración (login)
     * Simula múltiples solicitudes al endpoint de login de Filament
     */
    public function test_carga_panel_login(): void
    {
        $startTime = microtime(true);
        $successCount = 0;
        $responseTimes = [];

        for ($i = 0; $i < self::REQUEST_COUNT; $i++) {
            $requestStart = microtime(true);
            
            $response = $this->get('/informatica/login');
            
            $requestEnd = microtime(true);
            $responseTimes[] = ($requestEnd - $requestStart) * 1000;

            // 200 = página cargada, 302 = redirect (ambos son válidos)
            if (in_array($response->status(), [200, 302])) {
                $successCount++;
            }
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        $avgResponseTime = array_sum($responseTimes) / count($responseTimes);

        $this->assertGreaterThan(
            self::REQUEST_COUNT * 0.95, // 95% de éxito mínimo
            $successCount,
            "Demasiadas solicitudes fallaron"
        );

        echo "\n📊 Métricas de carga - Panel Login:";
        echo "\n   - Total solicitudes: " . self::REQUEST_COUNT;
        echo "\n   - Exitosas: {$successCount}";
        echo "\n   - Tiempo total: " . round($totalTime, 4) . " segundos";
        echo "\n   - Solicitudes por segundo: " . round(self::REQUEST_COUNT / $totalTime, 2);
        echo "\n   - Tiempo respuesta promedio: " . round($avgResponseTime, 2) . " ms";
    }

    /**
     * Test de solicitudes mixtas
     * Simula un patrón de uso real con diferentes rutas
     */
    public function test_solicitudes_mixtas(): void
    {
        $routes = [
            '/',
            '/informatica/login',
            '/inscripcion/login',
        ];

        $startTime = microtime(true);
        $results = [];
        $totalRequests = 0;

        foreach ($routes as $route) {
            $results[$route] = ['success' => 0, 'fail' => 0, 'times' => []];
            
            for ($i = 0; $i < 30; $i++) {
                $requestStart = microtime(true);
                
                $response = $this->get($route);
                
                $requestEnd = microtime(true);
                $results[$route]['times'][] = ($requestEnd - $requestStart) * 1000;
                $totalRequests++;

                if (in_array($response->status(), [200, 302, 404])) {
                    $results[$route]['success']++;
                } else {
                    $results[$route]['fail']++;
                }
            }
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;

        echo "\n📊 Métricas de solicitudes mixtas:";
        echo "\n   - Total solicitudes: {$totalRequests}";
        echo "\n   - Tiempo total: " . round($totalTime, 4) . " segundos";
        
        foreach ($results as $route => $data) {
            $avg = count($data['times']) > 0 ? array_sum($data['times']) / count($data['times']) : 0;
            echo "\n   - {$route}:";
            echo "\n     - Exitosas: {$data['success']}, Fallidas: {$data['fail']}";
            echo "\n     - Tiempo promedio: " . round($avg, 2) . " ms";
        }

        // Verificar que al menos el 90% de solicitudes fueron exitosas
        $totalSuccess = array_sum(array_column($results, 'success'));
        $this->assertGreaterThan($totalRequests * 0.9, $totalSuccess);
    }

    /**
     * Test de ráfaga de solicitudes
     * Simula una ráfaga de solicitudes rápidas
     */
    public function test_rafaga_solicitudes(): void
    {
        $startTime = microtime(true);
        $responses = [];

        // Ráfaga de 50 solicitudes lo más rápido posible
        for ($i = 0; $i < 50; $i++) {
            $responses[] = $this->get('/');
        }

        $endTime = microtime(true);
        $burstTime = $endTime - $startTime;

        $successCount = 0;
        foreach ($responses as $response) {
            if ($response->status() === 200) {
                $successCount++;
            }
        }

        $this->assertEquals(50, $successCount, "Algunas solicitudes de la ráfaga fallaron");

        echo "\n📊 Métricas de ráfaga:";
        echo "\n   - Solicitudes en ráfaga: 50";
        echo "\n   - Exitosas: {$successCount}";
        echo "\n   - Tiempo de ráfaga: " . round($burstTime, 4) . " segundos";
        echo "\n   - Solicitudes por segundo: " . round(50 / $burstTime, 2);
    }

    /**
     * Test de respuesta bajo carga con datos
     * Verifica que las respuestas contengan datos correctos bajo carga
     */
    public function test_integridad_respuesta_bajo_carga(): void
    {
        $startTime = microtime(true);
        $validResponses = 0;

        for ($i = 0; $i < 50; $i++) {
            $response = $this->get('/');
            
            // Verificar que la respuesta contiene contenido esperado
            if ($response->status() === 200) {
                $content = $response->getContent();
                // Verificar que tiene estructura HTML válida
                if (str_contains($content, '<html') || str_contains($content, '<!DOCTYPE')) {
                    $validResponses++;
                }
            }
        }

        $endTime = microtime(true);

        $this->assertEquals(50, $validResponses, "Algunas respuestas no tienen contenido válido");

        echo "\n📊 Métricas de integridad de respuesta:";
        echo "\n   - Solicitudes: 50";
        echo "\n   - Respuestas válidas: {$validResponses}";
        echo "\n   - Tiempo total: " . round($endTime - $startTime, 4) . " segundos";
    }

    /**
     * Test de latencia percentil
     * Calcula percentiles de latencia para análisis de rendimiento
     */
    public function test_latencia_percentil(): void
    {
        $responseTimes = [];

        for ($i = 0; $i < self::REQUEST_COUNT; $i++) {
            $start = microtime(true);
            $this->get('/');
            $end = microtime(true);
            $responseTimes[] = ($end - $start) * 1000;
        }

        sort($responseTimes);

        $p50 = $responseTimes[(int)(count($responseTimes) * 0.50)];
        $p90 = $responseTimes[(int)(count($responseTimes) * 0.90)];
        $p95 = $responseTimes[(int)(count($responseTimes) * 0.95)];
        $p99 = $responseTimes[(int)(count($responseTimes) * 0.99)];

        echo "\n📊 Análisis de percentiles de latencia:";
        echo "\n   - Total solicitudes: " . self::REQUEST_COUNT;
        echo "\n   - P50 (mediana): " . round($p50, 2) . " ms";
        echo "\n   - P90: " . round($p90, 2) . " ms";
        echo "\n   - P95: " . round($p95, 2) . " ms";
        echo "\n   - P99: " . round($p99, 2) . " ms";

        // El P95 debería ser razonable (menos de 500ms en entorno de prueba)
        $this->assertLessThan(500, $p95, "El P95 de latencia es demasiado alto");
    }
}
