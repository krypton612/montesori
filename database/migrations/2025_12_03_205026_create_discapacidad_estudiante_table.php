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
        Schema::create('discapacidad_estudiante', function (Blueprint $table) {
            $table->id(); // id int, PK, autoincremental

            // FK hacia discapacidad (nullable, por especificación del issue)
            $table->foreignId('discapacidad_id')
                ->nullable()
                ->constrained('discapacidad')
                ->nullOnDelete();

            // FK hacia estudiante (nullable, por especificación del issue)
            $table->foreignId('estudiante_id')
                ->nullable()
                ->constrained('estudiante')
                ->nullOnDelete();

            // Descripción u observación de la discapacidad
            $table->text('observacion')->nullable();

            // created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discapacidad_estudiante');
    }
};
