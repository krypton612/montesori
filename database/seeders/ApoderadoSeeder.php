<?php

namespace Database\Seeders;

use App\Models\Apoderado;
use App\Models\Persona;
use Illuminate\Database\Seeder;

class ApoderadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea algunas personas y sus apoderados asociados
        Persona::factory()
            ->count(10)
            ->create()
            ->each(function ($persona) {
                Apoderado::factory()->create([
                    'persona_id' => $persona->id,
                ]);
            });
    }
}
