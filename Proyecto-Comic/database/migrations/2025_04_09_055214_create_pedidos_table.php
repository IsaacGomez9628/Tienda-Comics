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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('id_pedido');
            $table->string('folio', 20)->unique();
            $table->foreignId('id_proveedor')->constrained('proveedores', 'id_proveedor');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario');
            $table->date('fecha_entrega_estimada')->nullable();
            $table->date('fecha_entrega_real')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->foreignId('id_moneda')->constrained('monedas', 'id_moneda');
            $table->foreignId('id_estado_pedido')->constrained('estado_pedidos', 'id_estado_pedido');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
