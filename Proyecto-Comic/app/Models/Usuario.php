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
        'id_user',
        'nombre_usuario',
        'contrasena',
        'id_rol',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function esAdmin()
    {
        // Si no tiene usuario relacionado, no es admin
        if (!$this->usuario) {
            return false;
        }
        
        // Verifica si el rol del usuario es "Administrador"
        return $this->usuario->rol->nombre === 'Administrador';
    }
}