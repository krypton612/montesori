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
            $table->dropColumn('inscripcion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_inscripcion', function (Blueprint $table) {
            $table->unsignedBigInteger('inscripcion_id')->after('id')->nullable(false);
            $table->foreign('inscripcion_id')->references('id')->on('inscripcion')->onDelete('cascade');
        });
    }
};
