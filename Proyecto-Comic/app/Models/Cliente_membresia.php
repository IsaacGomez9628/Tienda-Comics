<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente_membresia extends Model
{
    use HasFactory;
    
    protected $table = 'cliente_membresias';
    protected $primaryKey = 'id_cliente_membresia';
    
    protected $fillable = [
        'id_cliente',
        'id_membresia',
        'fecha_inicio',
        'fecha_fin',
        'id_estatus'
    ];
    
    protected $dates = [
        'fecha_inicio',
        'fecha_fin'
    ];
    
    // Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    
    // Relación con membresía
    public function membresia()
    {
        return $this->belongsTo(Membresia::class, 'id_membresia');
    }
    
    // Verificar si la membresía está activa
    public function estaActiva()
    {
        return ($this->fecha_fin === null || $this->fecha_fin >= now()) && $this->id_estatus == 1;
    }
}