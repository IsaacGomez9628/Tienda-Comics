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
        Schema::create('editoriales', function (Blueprint $table) {
            $table->id('id_editorial');
            $table->string('nombre', 100)->unique();
            $table->string('slug')->unique();
            $table->integer('anio_fundacion')->nullable();
            $table->text('descripcion')->nullable();
            $table->foreignId('id_estatus')->default(1)->constrained('estatus', 'id_estatus');
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('editoriales');
    }
};