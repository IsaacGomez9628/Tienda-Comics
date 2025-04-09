<?php

namespace App\Models;

// Usa las clases necesarias de Laravel

use Illuminate\Container\Attributes\Log;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación con el modelo Usuario
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_user');
    }

    // Método para verificar si es administrador
    public function esAdmin()
    {
        try {
            // Si no existe la relación usuario o no está cargada, intentar cargarla
            if (!$this->relationLoaded('usuario')) {
                $this->load('usuario.rol');
            }
            
            // Si no tiene usuario relacionado, no es admin
            if (!$this->usuario) {
                return false;
            }
            
            // Si el usuario no tiene rol cargado, intentar cargarlo
            if (!$this->usuario->relationLoaded('rol')) {
                $this->usuario->load('rol');
            }
            
            // Verifica si el rol existe y es "Administrador"
            return $this->usuario->rol && $this->usuario->rol->nombre === 'Administrador';
        } catch (\Exception $e) {
            // En caso de error, por seguridad devuelve false
            Log::error('Error en método esAdmin: ' . $e->getMessage());
            return false;
        }
    }
}