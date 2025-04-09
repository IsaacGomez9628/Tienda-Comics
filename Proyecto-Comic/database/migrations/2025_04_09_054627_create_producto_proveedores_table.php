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
        Schema::create('producto_proveedores', function (Blueprint $table) {
            $table->id('id_producto_proveedor');
            $table->foreignId('id_producto')->constrained('productos', 'id_producto');
            $table->foreignId('id_proveedor')->constrained('proveedores', 'id_proveedor');
            $table->boolean('es_proveedor_principal')->default(false);
            $table->decimal('precio_proveedor', 10, 2);
            $table->integer('tiempo_entrega_dias')->nullable();
            $table->text('notas')->nullable();
            $table->unique(['id_producto', 'id_proveedor']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_proveedores');
    }
};
