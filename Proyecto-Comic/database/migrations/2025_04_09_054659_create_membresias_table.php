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
        Schema::create('membresias', function (Blueprint $table) {
            $table->id('id_membresia');
            $table->string('nombre', 50)->unique();
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->integer('puntos_requeridos');
            $table->decimal('porcentaje_descuento', 5, 2)->default(0);
            $table->foreignId('id_estatus')->default(1)->constrained('estatus', 'id_estatus');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }
};
