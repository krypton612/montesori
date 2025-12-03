<?php

namespace Database\Seeders;

use App\Models\Estudiante;
use App\Models\Persona;
use Illuminate\Database\Seeder;

class EstudianteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ejemplo sencillo: por ahora asume que ya existen personas
        // y se crean estudiantes a partir de ellas.

        $personas = Persona::limit(10)->get();

        foreach ($personas as $persona) {
            Estudiante::create([
                'persona_id'        => $persona->id,
                'codigo_saga'       => null,
                'estado_academico'  => 'regular',
                'tiene_discapacidad'=> false,
                'observaciones'     => null,
                'foto_url'          => null,
            ]);
        }
    }
}
