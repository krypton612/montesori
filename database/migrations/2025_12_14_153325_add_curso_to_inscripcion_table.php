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
        Schema::table('inscripcion', function (Blueprint $table) {
            $table->unsignedBigInteger('curso_id')->after('id')->nullable(false);
            $table->foreign('curso_id')->references('id')->on('curso')->onDelete('cascade');
            $table->unique(['curso_id', 'estudiante_id', 'gestion_id'], 'inscripcion_curso_estudiante_gestion_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscripcion', function (Blueprint $table) {
            $table->dropUnique('inscripcion_curso_estudiante_gestion_unique');
            $table->dropForeign(['curso_id']);
            $table->dropColumn('curso_id');
        });
    }
};
