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
        Schema::create('discapacidad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->string('codigo', 50)->unique();
            $table->foreignId('tipo_discapacidad_id')
                ->constrained('tipo_discapacidad'); // referencia a id

            $table->boolean('requiere_acompaniante')->default(false);
            $table->boolean('necesita_equipo_especial')->default(false);
            $table->boolean('requiere_adaptacion_curricular')->default(false);
            $table->boolean('visible')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discapacidad');
    }
};