<?php

namespace Database\Seeders;

use App\Models\Discapacidad;
use App\Models\DiscapacidadEstudiante;
use App\Models\Estudiante;
use Illuminate\Database\Seeder;

class DiscapacidadEstudianteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estudiantes    = Estudiante::limit(10)->get();
        $discapacidades = Discapacidad::limit(5)->get();

        // Si no hay datos base, no hacemos nada
        if ($estudiantes->isEmpty() || $discapacidades->isEmpty()) {
            return;
        }

        foreach ($estudiantes as $estudiante) {
            // Asignar de 1 a 3 discapacidades aleatorias a cada estudiante
            $asignadas = $discapacidades
                ->shuffle()
                ->take(rand(1, min(3, $discapacidades->count())));

            foreach ($asignadas as $discapacidad) {
                DiscapacidadEstudiante::create([
                    'discapacidad_id' => $discapacidad->id,
                    'estudiante_id'   => $estudiante->id,
                    'observacion'     => 'Asignación de discapacidad de prueba',
                ]);
            }
        }
    }
}
