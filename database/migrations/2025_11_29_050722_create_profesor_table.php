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
        Schema::create('profesor', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('persona_id')->nullable();
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('set null');
            $table->string('codigo_saga')->unique();
            $table->boolean('habilitado')->default(true);
            $table->string('nacionalidad')->nullable();
            $table->string('foto_url')->nullable();
            $table->string('anios_experiencia')->nullable();
            $table->string('profesion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesor');
    }
};
