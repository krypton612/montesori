<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\Estado;
use App\Models\Evaluacion;
use App\Models\TipoEvaluacion;
use Carbon\Carbon;

class EvaluacionGeneratorService
{
    /**
     * Genera evaluaciones según el tipo de evaluación
     */
    public function generarEvaluaciones(Curso $curso, array $tiposEvaluacionIds): void
    {
        foreach ($tiposEvaluacionIds as $tipoId) {
            $tipoEvaluacion = TipoEvaluacion::find($tipoId);

            if (!$tipoEvaluacion) {
                continue;
            }

            // Estrategia según el nombre del tipo
            $this->aplicarEstrategia($curso, $tipoEvaluacion);
        }
    }

    /**
     * Aplica la estrategia según el tipo de evaluación
     */
    protected function aplicarEstrategia(Curso $curso, TipoEvaluacion $tipo): void
    {
        $nombreTipo = strtolower($tipo->nombre);

        // Estrategias según el nombre del tipo
        if (str_contains($nombreTipo, 'parcial') || str_contains($nombreTipo, 'examen parcial')) {
            $this->generarExamenesParciales($curso, $tipo);
        }
        elseif (str_contains($nombreTipo, 'final') || str_contains($nombreTipo, 'examen final')) {
            $this->generarExamenFinal($curso, $tipo);
        }
        elseif (str_contains($nombreTipo, 'tarea') || str_contains($nombreTipo, 'deber')) {
            $this->generarTareas($curso, $tipo);
        }
        elseif (str_contains($nombreTipo, 'proyecto')) {
            $this->generarProyecto($curso, $tipo);
        }
        elseif (str_contains($nombreTipo, 'quiz') || str_contains($nombreTipo, 'prueba corta')) {
            $this->generarQuizzes($curso, $tipo);
        }
        elseif (str_contains($nombreTipo, 'diagnóstico') || str_contains($nombreTipo, 'diagnostico')) {
            $this->generarEvaluacionDiagnostica($curso, $tipo);
        }
        elseif (str_contains($nombreTipo, 'práctica') || str_contains($nombreTipo, 'laboratorio')) {
            $this->generarPracticas($curso, $tipo);
        }
        elseif (str_contains($nombreTipo, 'exposición') || str_contains($nombreTipo, 'presentacion')) {
            $this->generarExposiciones($curso, $tipo);
        }
        else {
            // Estrategia genérica
            $this->generarEvaluacionGenerica($curso, $tipo);
        }
    }

    /**
     * Genera 3 exámenes parciales distribuidos en el semestre
     */
    protected function generarExamenesParciales(Curso $curso, TipoEvaluacion $tipo): void
    {
        $fechaInicio = Carbon::now();
        $duracionSemestre = 120; // días aproximados
        $intervalo = $duracionSemestre / 3;

        for ($i = 1; $i <= 3; $i++) {
            $fechaExamen = $fechaInicio->copy()->addDays($intervalo * $i);

            $this->crearEvaluacion($curso, $tipo, [
                'titulo' => "Examen Parcial {$i}",
                'descripcion' => "Evaluación parcial {$i} del curso",
                'fecha_inicio' => $fechaExamen->format('Y-m-d'),
                'fecha_fin' => $fechaExamen->copy()->addHours(2)->format('Y-m-d'),
                'visible' => false,
            ]);
        }
    }

    /**
     * Genera 1 examen final al término del curso
     */
    protected function generarExamenFinal(Curso $curso, TipoEvaluacion $tipo): void
    {
        $fechaFinal = Carbon::now()->addDays(120); // Al final del semestre

        $this->crearEvaluacion($curso, $tipo, [
            'titulo' => 'Examen Final',
            'descripcion' => 'Evaluación final del curso',
            'fecha_inicio' => $fechaFinal->format('Y-m-d'),
            'fecha_fin' => $fechaFinal->copy()->addHours(3)->format('Y-m-d'),
            'visible' => false,
        ]);
    }

    /**
     * Genera 10 tareas semanales
     */
    protected function generarTareas(Curso $curso, TipoEvaluacion $tipo): void
    {
        $fechaInicio = Carbon::now();

        for ($i = 1; $i <= 10; $i++) {
            $fechaTarea = $fechaInicio->copy()->addWeeks($i);

            $this->crearEvaluacion($curso, $tipo, [
                'titulo' => "Tarea {$i}",
                'descripcion' => "Tarea semanal {$i}",
                'fecha_inicio' => $fechaTarea->format('Y-m-d'),
                'fecha_fin' => $fechaTarea->copy()->addDays(7)->format('Y-m-d'),
                'visible' => false,
            ]);
        }
    }

    /**
     * Genera 1 proyecto final
     */
    protected function generarProyecto(Curso $curso, TipoEvaluacion $tipo): void
    {
        $fechaInicio = Carbon::now()->addDays(60); // Mitad del semestre
        $fechaFin = Carbon::now()->addDays(110);

        $this->crearEvaluacion($curso, $tipo, [
            'titulo' => 'Proyecto Final',
            'descripcion' => 'Proyecto integrador del curso',
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'visible' => false,
        ]);
    }

    /**
     * Genera 12 quizzes (uno por semana)
     */
    protected function generarQuizzes(Curso $curso, TipoEvaluacion $tipo): void
    {
        $fechaInicio = Carbon::now();

        for ($i = 1; $i <= 12; $i++) {
            $fechaQuiz = $fechaInicio->copy()->addWeeks($i);

            $this->crearEvaluacion($curso, $tipo, [
                'titulo' => "Quiz {$i}",
                'descripcion' => "Prueba corta semana {$i}",
                'fecha_inicio' => $fechaQuiz->format('Y-m-d'),
                'fecha_fin' => $fechaQuiz->copy()->addMinutes(30)->format('Y-m-d'),
                'visible' => false,
            ]);
        }
    }

    /**
     * Genera 1 evaluación diagnóstica al inicio
     */
    protected function generarEvaluacionDiagnostica(Curso $curso, TipoEvaluacion $tipo): void
    {
        $fechaDiagnostico = Carbon::now()->addDays(3); // Primeros días de clase

        $this->crearEvaluacion($curso, $tipo, [
            'titulo' => 'Evaluación Diagnóstica',
            'descripcion' => 'Evaluación inicial para conocer el nivel de los estudiantes',
            'fecha_inicio' => $fechaDiagnostico->format('Y-m-d'),
            'fecha_fin' => $fechaDiagnostico->copy()->addHours(1)->format('Y-m-d'),
            'visible' => false,
        ]);
    }

    /**
     * Genera 8 prácticas de laboratorio
     */
    protected function generarPracticas(Curso $curso, TipoEvaluacion $tipo): void
    {
        $fechaInicio = Carbon::now();

        for ($i = 1; $i <= 8; $i++) {
            $fechaPractica = $fechaInicio->copy()->addWeeks($i * 1.5);

            $this->crearEvaluacion($curso, $tipo, [
                'titulo' => "Práctica de Laboratorio {$i}",
                'descripcion' => "Práctica {$i}",
                'fecha_inicio' => $fechaPractica->format('Y-m-d'),
                'fecha_fin' => $fechaPractica->copy()->addDays(7)->format('Y-m-d'),
                'visible' => false,
            ]);
        }
    }

    /**
     * Genera 4 exposiciones grupales
     */
    protected function generarExposiciones(Curso $curso, TipoEvaluacion $tipo): void
    {
        $fechaInicio = Carbon::now()->addDays(30);
        $intervalo = 20; // días entre exposiciones

        for ($i = 1; $i <= 4; $i++) {
            $fechaExposicion = $fechaInicio->copy()->addDays($intervalo * $i);

            $this->crearEvaluacion($curso, $tipo, [
                'titulo' => "Exposición {$i}",
                'descripcion' => "Presentación grupal {$i}",
                'fecha_inicio' => $fechaExposicion->format('Y-m-d'),
                'fecha_fin' => $fechaExposicion->format('Y-m-d'),
                'visible' => false,
            ]);
        }
    }

    /**
     * Estrategia genérica: crea 1 evaluación
     */
    protected function generarEvaluacionGenerica(Curso $curso, TipoEvaluacion $tipo): void
    {
        $this->crearEvaluacion($curso, $tipo, [
            'titulo' => $tipo->nombre,
            'descripcion' => 'Evaluación pendiente de configuración',
            'fecha_inicio' => Carbon::now()->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'visible' => false,
        ]);
    }

    /**
     * Crea una evaluación en la base de datos
     */
    protected function crearEvaluacion(Curso $curso, TipoEvaluacion $tipo, array $datos): void
    {
        // Obtener el año actual de la gestión
        $anioGestion = $curso->gestion->nombre ?? date('Y');

        // Generar NIO único: GESTION-CURSO-TIPO-NUMERO
        $numeroEvaluaciones = Evaluacion::where('curso_id', $curso->id)
                ->where('tipo_evaluacion_id', $tipo->id)
                ->count() + 1;



        Evaluacion::create([
            'tipo_evaluacion_id' => $tipo->id,
            'curso_id' => $curso->id,
            'gestion_id' => $curso->gestion_id,
            'estado_id' => 12, // Estado "Pendiente" por defecto
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'fecha_inicio' => $datos['fecha_inicio'],
            'fecha_fin' => $datos['fecha_fin'],
            'visible' => $datos['visible'] ?? false,
        ]);
    }
}
