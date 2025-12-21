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
        Schema::create('curso', function (Blueprint $table) {
            $table->id();
            $table->string('seccion');
            $table->integer('cupo_maximo');
            $table->integer('cupo_minimo');
            $table->integer('cupo_actual')->default(0);

            $table->unsignedBigInteger('profesor_id')->nullable();
            $table->foreign('profesor_id')->references('id')->on('profesor')->onDelete('set null');

            $table->unsignedBigInteger('materia_id')->nullable();
            $table->foreign('materia_id')->references('id')->on('materia')->onDelete('set null');

            $table->unsignedBigInteger('estado_id')->nullable();
            $table->foreign('estado_id')->references('id')->on('estado')->onDelete('set null');

            $table->unsignedBigInteger('turno_id')->nullable();
            $table->foreign('turno_id')->references('id')->on('turno')->onDelete('set null');

            $table->unsignedBigInteger('gestion_id')->nullable();
            $table->foreign('gestion_id')->references('id')->on('gestion')->onDelete('set null');

            $table->boolean('habilitado')->default(true);


            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso');
    }
};
