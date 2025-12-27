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
        Schema::create('nota_estudiante', function (Blueprint $table) {
            $table->id();
            $table->decimal('nota', 5, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('evaluacion_id');
            $table->unsignedBigInteger('estado_id');

            $table->foreign('estudiante_id')->references('id')->on('estudiante')->onDelete('cascade');
            $table->foreign('evaluacion_id')->references('id')->on('evaluacion')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('estado')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_estudiante');
    }
};
