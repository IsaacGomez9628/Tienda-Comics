<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membresia extends Model
{
    use HasFactory;
    
    protected $table = 'membresias';
    protected $primaryKey = 'id_membresia';
    
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'puntos_requeridos',
        'porcentaje_descuento',
        'id_estatus'
    ];
    
    // Relación con cliente-membresias
    public function clienteMembresias()
    {
        return $this->hasMany(Cliente_membresia::class, 'id_membresia');
    }
    
    // Clientes con esta membresía
    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_membresias', 'id_membresia', 'id_cliente')
                    ->withPivot('fecha_inicio', 'fecha_fin', 'id_estatus')
                    ->withTimestamps();
    }
}