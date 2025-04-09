<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;
    
    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'id_estatus'
    ];
    
    // Relación con usuarios
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }
    
    // Relación con permisos
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permisos', 'id_rol', 'id_permiso')
                    ->withTimestamps();
    }
}