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
        Schema::create('documento_inscripcion', function (Blueprint $table) {;
            $table->id();

            $table->string('nombre_archivo');
            $table->unsignedBigInteger('inscripcion_id');
            $table->unsignedBigInteger('tipo_documento_id');
            $table->foreign('inscripcion_id')->references('id')->on('inscripcion')->onDelete('set null');
            $table->foreign('tipo_documento_id')->references('id')->on('tipo_documento')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_inscripcion');
    }
};
