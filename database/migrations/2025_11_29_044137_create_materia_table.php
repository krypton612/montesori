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
        Schema::create('materia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('nivel')->nullable();
            $table->integer('horas_semanales')->nullable();
            $table->string('descripcion')->nullable();
            $table->boolean('habilitado')->default(true);
            $table->timestamps();
        });

        Schema::create('malla_curricular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')->references('id')->on('materia')->onDelete('set null');
            $table->integer('anio')->nullable();
            $table->string('nombre_archivo')->nullable();
            $table->boolean('habilitado')->default(true);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('malla_curricular');
        Schema::dropIfExists('materia');
    }
};
