<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    
    protected $fillable = [
        'id_persona',
        'codigo_cliente',
        'puntos_acumulados',
        'id_estatus'
    ];
    
    // Relación con persona
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
    
    // Relación con membresías
    public function membresias()
    {
        return $this->hasMany(Cliente_membresia::class, 'id_cliente');
    }
    
    // Obtener membresía actual
    public function membresiaActual()
    {
        return $this->membresias()
                    ->where('fecha_fin', '>=', now())
                    ->orWhereNull('fecha_fin')
                    ->where('id_estatus', 1)
                    ->orderBy('id_membresia', 'desc')
                    ->first();
    }
    
    // Relación con ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente');
    }
    
    // Relación con historial de compras
    public function historialCompras()
    {
        return $this->hasMany(Historial_Compra::class, 'id_cliente');
    }
    
    // Sumar puntos
    public function sumarPuntos($puntos)
    {
        $this->puntos_acumulados += $puntos;
        $this->save();
        
        // Verificar si puede subir de nivel de membresía
        $this->verificarNivelMembresia();
        
        return $this->puntos_acumulados;
    }
    
    // Verificar si puede subir de nivel de membresía
    public function verificarNivelMembresia()
    {
        $membresiasDisponibles = Membresia::where('puntos_requeridos', '<=', $this->puntos_acumulados)
                                      ->orderBy('puntos_requeridos', 'desc')
                                      ->get();
        
        if ($membresiasDisponibles->isNotEmpty()) {
            $nuevaMembresia = $membresiasDisponibles->first();
            $membresiaActual = $this->membresiaActual();
            
            // Si no tiene membresía o puede subir de nivel
            if (!$membresiaActual || $membresiaActual->id_membresia < $nuevaMembresia->id_membresia) {
                // Cerrar membresía actual si existe
                if ($membresiaActual) {
                    $membresiaActual->fecha_fin = now();
                    $membresiaActual->save();
                }
                
                // Crear nueva membresía
                Cliente_membresia::create([
                    'id_cliente' => $this->id_cliente,
                    'id_membresia' => $nuevaMembresia->id_membresia,
                    'fecha_inicio' => now(),
                    'fecha_fin' => null,
                    'id_estatus' => 1
                ]);
                
                return true;
            }
        }
        
        return false;
    }
}