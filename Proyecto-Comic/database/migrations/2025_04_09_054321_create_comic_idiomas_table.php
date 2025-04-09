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
        Schema::create('comic_idiomas', function (Blueprint $table) {
            $table->id('id_comic_idioma');
            $table->foreignId('id_comic')->constrained('comics', 'id_comic');
            $table->foreignId('id_idioma')->constrained('idiomas', 'id_idioma');
            $table->boolean('es_idioma_original')->default(false);
            $table->unique(['id_comic', 'id_idioma']);
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('comic_idiomas');
    }
};