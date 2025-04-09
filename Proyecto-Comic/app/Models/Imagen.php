<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    use HasFactory;

    protected $table = 'imagenes';
    protected $primaryKey = 'id_imagen';
    
    protected $fillable = [
        'ruta',
        'nombre_original',
        'extension',
        'mime_type',
        'alt_texto',
        'titulo',
        'descripcion',
        'orden',
        'es_principal',
        'tamanio',
        'id_estatus'
    ];

    /**
     * Obtiene el modelo propietario de la imagen (cómic, figura, pedido, etc.)
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}