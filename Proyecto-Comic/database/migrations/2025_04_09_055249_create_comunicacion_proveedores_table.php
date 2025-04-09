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
        Schema::create('comunicacion_proveedores', function (Blueprint $table) {
            $table->id('id_comunicacion');
            $table->foreignId('id_pedido')->constrained('pedidos', 'id_pedido');
            $table->string('asunto', 100);
            $table->text('contenido');
            $table->string('email_destino', 100);
            $table->string('email_cc', 100)->nullable();
            $table->enum('estatus', ['enviado', 'recibido', 'confirmado', 'rechazado'])->default('enviado');
            $table->text('respuesta')->nullable();
            $table->datetime('fecha_respuesta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comunicacion_proveedores');
    }
};
