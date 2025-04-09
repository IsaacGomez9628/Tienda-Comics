<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->foreignId('id_persona')->constrained('personas', 'id_persona');
            $table->string('nombre_usuario', 50)->unique();
            $table->string('contrasena', 255);
            $table->foreignId('id_rol')->constrained('roles', 'id_rol');
            $table->datetime('ultima_sesion')->nullable();
            $table->foreignId('id_estatus')->default(1)->constrained('estatus', 'id_estatus');
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};