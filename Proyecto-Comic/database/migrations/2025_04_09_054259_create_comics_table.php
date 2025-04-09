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
        Schema::create('comics', function (Blueprint $table) {
            $table->id('id_comic');
            $table->foreignId('id_producto')->constrained('productos', 'id_producto');
            $table->string('numero_edicion', 20);
            $table->string('isbn', 20)->nullable();
            $table->string('escritor', 100)->nullable();
            $table->string('ilustrador', 100)->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->integer('numero_paginas')->nullable();
            $table->foreignId('id_idioma')->constrained('idiomas', 'id_idioma');
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('comics');
    }
};