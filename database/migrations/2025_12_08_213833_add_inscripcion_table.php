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
        Schema::create('inscripcion', function (Blueprint $table) {;
            $table->id();
            $table->string('codigo_inscripcion')->unique();
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('gestion_id');
            $table->date('fecha_inscripcion');
            $table->unsignedBigInteger('estado_id');
            $table->foreign('estudiante_id')->references('id')->on('estudiante')->onDelete('set null');
            $table->foreign('grupo_id')->references('id')->on('grupo')->onDelete('set null');
            $table->foreign('gestion_id')->references('id')->on('gestion')->onDelete('set null');
            $table->foreign('estado_id')->references('id')->on('estado')->onDelete('set null');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcion');
    }
};
