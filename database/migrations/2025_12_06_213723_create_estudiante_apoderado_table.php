<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estudiante_apoderado', function (Blueprint $table) {
            $table->id(); // PK autoincremental

            // FK a estudiante (obligatoria)
            $table->foreignId('estudiante_id')
                ->constrained('estudiante')
                ->cascadeOnDelete();

            // FK a apoderado (obligatoria)
            $table->foreignId('apoderado_id')
                ->constrained('apoderado')
                ->cascadeOnDelete();

            // Parentesco (no nullable)
            $table->string('parentestco', 255);

            // Banderas booleanas
            $table->boolean('vive_con_el')->default(false);
            $table->boolean('es_principal')->default(false);

            // created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante_apoderado');
    }
};
