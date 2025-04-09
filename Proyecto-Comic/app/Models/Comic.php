<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    use HasFactory;

    protected $table = 'comics';
    protected $primaryKey = 'id_comic';
    
    // ... tus otros atributos y métodos ...

    /**
     * Obtiene las imágenes asociadas al cómic
     */
    public function imagenes()
    {
        return $this->morphMany(Imagen::class, 'imageable');
    }
    
    /**
     * Obtiene la imagen principal del cómic
     */
    public function imagenPrincipal()
    {
        return $this->morphOne(Imagen::class, 'imageable')
                    ->where('es_principal', true);
    }
}