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
        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->id('id_rol_permiso');
            $table->foreignId('id_rol')->constrained('roles', 'id_rol');
            $table->foreignId('id_permiso')->constrained('permisos', 'id_permiso');
            $table->unique(['id_rol', 'id_permiso']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol_permisos');
    }
};
