<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Figura extends Model
{
    use HasFactory;

    protected $table = 'figuras';
    protected $primaryKey = 'id_figura';
    
    protected $fillable = [
        'id_producto',
        'material',
        'altura',
        'peso',
        'escala',
        'personaje',
        'serie',
        'artista',
        'edicion_limitada',
        'numero_serie'
    ];

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    // Obtiene las imágenes asociadas a la figura
    public function imagenes()
    {
        return $this->morphMany(Imagen::class, 'imageable');
    }
    
    // Obtiene la imagen principal de la figura
    public function imagenPrincipal()
    {
        return $this->morphOne(Imagen::class, 'imageable')
                    ->where('es_principal', true);
    }
}