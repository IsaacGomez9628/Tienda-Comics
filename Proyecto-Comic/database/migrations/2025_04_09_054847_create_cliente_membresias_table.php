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
        Schema::create('cliente_membresias', function (Blueprint $table) {
            $table->id('id_cliente_membresia');
            $table->foreignId('id_cliente')->constrained('clientes', 'id_cliente');
            $table->foreignId('id_membresia')->constrained('membresias', 'id_membresia');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->foreignId('id_estatus')->default(1)->constrained('estatus', 'id_estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_membresias');
    }
};
