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
        Schema::create('imagenes', function (Blueprint $table) {
            $table->id('id_imagen');
            $table->string('ruta');                          // Ruta donde se almacena la imagen
            $table->string('nombre_original')->nullable();   // Nombre original del archivo
            $table->string('extension', 10);                 // Extensión del archivo (jpg, png, etc.)
            $table->string('mime_type', 100)->nullable();    // Tipo MIME del archivo
            $table->string('alt_texto')->nullable();         // Texto alternativo para accesibilidad
            $table->string('titulo')->nullable();            // Título de la imagen
            $table->text('descripcion')->nullable();         // Descripción de la imagen
            $table->integer('orden')->default(0);            // Para ordenar múltiples imágenes
            $table->boolean('es_principal')->default(false); // Para marcar una imagen como principal
            $table->unsignedBigInteger('tamanio')->nullable(); // Tamaño del archivo en bytes
            
            // Campos polimórficos
            $table->string('imageable_type');               // El tipo de modelo (ej: App\Models\Comic)
            $table->unsignedBigInteger('imageable_id');     // El ID del modelo al que pertenece
            $table->index(['imageable_type', 'imageable_id']);
            
            $table->foreignId('id_estatus')->default(1)->constrained('estatus', 'id_estatus');
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};