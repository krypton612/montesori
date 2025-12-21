<?php

namespace Database\Seeders;

use App\Models\Discapacidad;
use App\Models\TipoDiscapacidad;
use Illuminate\Database\Seeder;

class DiscapacidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoVisual = TipoDiscapacidad::where('nombre', 'Discapacidad Visual')->first();
        $tipoAuditiva = TipoDiscapacidad::where('nombre', 'Discapacidad Auditiva')->first();

        if ($tipoVisual) {
            Discapacidad::create([
                'nombre' => 'Baja visión',
                'descripcion' => 'Discapacidad relacionada con la reducción significativa de la agudeza visual.',
                'codigo' => 'VIS-001',
                'tipo_discapacidad_id' => $tipoVisual->id,
                'requiere_acompaniante' => false,
                'necesita_equipo_especial' => true,
                'requiere_adaptacion_curricular' => true,
                'visible' => true,
            ]);
        }

        if ($tipoAuditiva) {
            Discapacidad::create([
                'nombre' => 'Hipoacusia moderada',
                'descripcion' => 'Discapacidad auditiva con pérdida parcial de la audición.',
                'codigo' => 'AUD-001',
                'tipo_discapacidad_id' => $tipoAuditiva->id,
                'requiere_acompaniante' => false,
                'necesita_equipo_especial' => true,
                'requiere_adaptacion_curricular' => true,
                'visible' => true,
            ]);
        }
    }
}
