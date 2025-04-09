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
        Schema::create('codigos_postales', function (Blueprint $table) {
            $table->id('id_codigo_postal');
            $table->string('codigo', 10);
            $table->foreignId('id_ciudad')->constrained('ciudades', 'id_ciudad');
            $table->foreignId('id_estatus')->default(1)->constrained('estatus', 'id_estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigos_postales');
    }
};
