<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    
    protected $fillable = [
        'id_persona',
        'nombre_usuario',
        'contrasena',
        'id_rol',
        'ultima_sesion',
        'id_estatus'
    ];
    
    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'ultima_sesion' => 'datetime',
    ];
    
    // Columna usada para autenticación (password)
    public function getAuthPassword()
    {
        return $this->contrasena;
    }
    
    // Para autenticación con email
    public function getEmailForPasswordReset()
    {
        return $this->persona->email;
    }
    
    // Método para verificar el rol
    public function esAdmin()
    {
        return $this->rol->nombre === 'Administrador';
    }
    
    // Método para verificar si es empleado
    public function esEmpleado()
    {
        return $this->rol->nombre === 'Empleado';
    }
    
    // Relación con Persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
    
    // Relación con Rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }
    
    // Relación con Ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_usuario');
    }
    
    // Relación con Pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_usuario');
    }
    
    // Relación con Movimientos
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'id_usuario');
    }
}