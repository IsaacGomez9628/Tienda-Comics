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
        Schema::create('figuras', function (Blueprint $table) {
            $table->id('id_figura');
            $table->foreignId('id_producto')->constrained('productos', 'id_producto');
            $table->string('material', 50)->nullable();
            $table->decimal('altura', 10, 2)->nullable();
            $table->decimal('peso', 10, 2)->nullable();
            $table->string('escala', 20)->nullable();
            $table->string('personaje', 100)->nullable();
            $table->string('serie', 100)->nullable();
            $table->string('artista', 100)->nullable();
            $table->boolean('edicion_limitada')->default(false);
            $table->string('numero_serie', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('figuras');
    }
};