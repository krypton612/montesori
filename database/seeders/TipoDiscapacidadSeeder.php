<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoDiscapacidad;

class TipoDiscapacidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoDiscapacidad::insert([
            [
                'nombre' => 'Discapacidad Visual',
                'descripcion' => 'Limitación total o parcial de la visión.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Discapacidad Auditiva',
                'descripcion' => 'Limitación total o parcial de la audición.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
