<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Figura extends Model
{
    use HasFactory;

    protected $table = 'figuras';
    protected $primaryKey = 'id_figura';
    

    /**
     * Obtiene las imágenes asociadas a la figura
     */
    public function imagenes()
    {
        return $this->morphMany(Imagen::class, 'imageable');
    }
    
    /**
     * Obtiene la imagen principal de la figura
     */
    public function imagenPrincipal()
    {
        return $this->morphOne(Imagen::class, 'imageable')
                    ->where('es_principal', true);
    }
}