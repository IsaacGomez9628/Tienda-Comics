<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    use HasFactory;

    protected $table = 'comics';
    protected $primaryKey = 'id_comic';
    
    protected $fillable = [
        'id_producto',
        'numero_edicion',
        'isbn',
        'escritor',
        'ilustrador',
        'fecha_publicacion',
        'numero_paginas',
        'id_idioma'
    ];
    
    protected $dates = [
        'fecha_publicacion'
    ];
    
    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    
    // Relación con idioma
    public function idioma()
    {
        return $this->belongsTo(Idioma::class, 'id_idioma');
    }
    
    // Relación con idiomas adicionales
    public function idiomas()
    {
        return $this->belongsToMany(Idioma::class, 'comic_idiomas', 'id_comic', 'id_idioma')
                    ->withPivot('es_idioma_original')
                    ->withTimestamps();
    }

    // Obtiene las imágenes asociadas al cómic
    public function imagenes()
    {
        return $this->morphMany(Imagen::class, 'imageable');
    }
    
    // Obtiene la imagen principal del cómic
    public function imagenPrincipal()
    {
        return $this->morphOne(Imagen::class, 'imageable')
                    ->where('es_principal', true);
    }
}