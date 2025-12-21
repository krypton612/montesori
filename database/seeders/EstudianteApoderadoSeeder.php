<?php

namespace Database\Seeders;

use App\Models\Apoderado;
use App\Models\Estudiante;
use App\Models\EstudianteApoderado;
use Illuminate\Database\Seeder;

class EstudianteApoderadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Si ya tienes estudiantes y apoderados en BD, úsalo.
        $estudiantes = Estudiante::all();
        $apoderados  = Apoderado::all();

        if ($estudiantes->isEmpty() || $apoderados->isEmpty()) {
            // Si no hay datos, crea algunos de prueba
            $estudiantes = Estudiante::factory()->count(5)->create();
            $apoderados  = Apoderado::factory()->count(5)->create();
        }

        foreach ($estudiantes as $estudiante) {
            // asignar 1–2 apoderados aleatorios
            $asignados = $apoderados->shuffle()->take(rand(1, 2));

            foreach ($asignados as $apoderado) {
                EstudianteApoderado::create([
                    'estudiante_id' => $estudiante->id,
                    'apoderado_id'  => $apoderado->id,
                    'parentestco'   => 'Padre',
                    'vive_con_el'   => true,
                    'es_principal'  => true,
                ]);
            }
        }
    }
}
