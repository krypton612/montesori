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
        Schema::create('estudiante', function (Blueprint $table) {
            $table->id(); // id int, PK, autoincremental

            // persona_id int no null - referencia a persona
            $table->foreignId('persona_id')
                ->constrained('persona')
                ->cascadeOnDelete();

            // código asignado por SAGA (nullable, sin unique)
            $table->string('codigo_saga')->nullable();

            // estado académico actual (nullable)
            $table->string('estado_academico')->nullable();

            // indica si posee discapacidad (no null, default false)
            $table->boolean('tiene_discapacidad')->default(false);

            // observaciones (nullable)
            $table->text('observaciones')->nullable();

            // URL de la foto del estudiante (nullable)
            $table->string('foto_url')->nullable();

            // deleted_at (soft delete)
            $table->softDeletes();

            // created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante');
    }
};
