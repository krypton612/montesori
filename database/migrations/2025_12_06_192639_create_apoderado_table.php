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
        Schema::create('apoderado', function (Blueprint $table) {
            $table->id(); // PK

            // Relación con persona (obligatoria)
            $table->foreignId('persona_id')
                ->constrained('persona')
                ->cascadeOnDelete();

            // Datos laborales y personales (todos opcionales)
            $table->string('ocupacion', 255)->nullable();
            $table->string('empresa', 255)->nullable();
            $table->string('cargo_empresa', 255)->nullable();
            $table->string('nivel_educacion', 255)->nullable();
            $table->string('estado_civil', 255)->nullable();

            // Soft delete
            $table->softDeletes();

            // created_at, updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apoderado');
    }
};
