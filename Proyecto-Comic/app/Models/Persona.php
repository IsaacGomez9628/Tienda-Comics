<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    
    protected $table = 'personas';
    protected $primaryKey = 'id_persona';
    
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'telefono',
        'email',
        'id_direccion',
        'id_estatus'
    ];
    
    // Nombre completo
    public function nombreCompleto()
    {
        return $this->nombre . ' ' . $this->apellido_paterno . ' ' . 
               ($this->apellido_materno ? $this->apellido_materno : '');
    }
    
    // Relación con dirección
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }
    
    // Relación con Usuario
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_persona');
    }
    
    // Relación con Cliente
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id_persona');
    }
}