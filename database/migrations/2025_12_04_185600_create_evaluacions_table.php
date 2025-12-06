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
        Schema::create('evaluacion', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->unsignedBigInteger('tipo_evaluacion_id');
            $table->foreign('tipo_evaluacion_id')->references('id')->on('tipo_evaluacion')->onDelete('set null');
            $table->unsignedBigInteger('curso_id');
            $table->foreign('curso_id')->references('id')->on('curso')->onDelete('set null');
            $table->unsignedBigInteger('estado_id');
            $table->foreign('estado_id')->references('id')->on('estado')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluacion');
    }
};
