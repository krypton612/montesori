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
        Schema::create('persona', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
            $table->string('nombre');
            $table->string('apellido_pat');
            $table->string('apellido_mat')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('edad')->nullable();
            $table->string('telefono_principal')->nullable();
            $table->string('telefono_secundario')->nullable();
            $table->string('email_personal')->nullable();
            $table->string('direccion')->nullable();
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
        Schema::dropIfExists('persona');
    }
};
