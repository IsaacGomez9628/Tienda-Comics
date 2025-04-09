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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id('id_proveedor');
            $table->string('nombre_empresa', 100);
            $table->string('slug')->unique();
            $table->foreignId('id_persona_contacto')->nullable()->constrained('personas','id_persona');
            $table->string('telefono', 15);
            $table->string('email', 100);
            $table->foreignId('id_direccion')->nullable()->constrained('direcciones', 'id_direccion');
            $table->string('pagina_web', 100)->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('id_estatus')->default(1)->constrained('estatus', 'id_estatus');
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};