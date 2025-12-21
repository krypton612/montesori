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
        Schema::table('documento_inscripcion', function (Blueprint $table) {
            $table->unsignedBigInteger('estudiante_id')->after('id')->nullable(false);
            $table->foreign('estudiante_id')->references('id')->on('estudiante')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_inscripcion', function (Blueprint $table) {
            $table->dropForeign(['estudiante_id']);
            $table->dropColumn('estudiante_id');
        });
    }
};
