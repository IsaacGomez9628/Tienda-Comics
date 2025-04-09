<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    
    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';
    
    protected $fillable = [
        'folio',
        'id_cliente',
        'id_usuario',
        'subtotal',
        'descuento',
        'impuesto',
        'total',
        'id_moneda',
        'metodo_pago',
        'estatus'
    ];
    
    // Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    
    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
    
    // Relación con moneda
    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda');
    }
    
    // Relación con detalles de venta
    public function detalles()
    {
        return $this->hasMany(Detalle_venta::class, 'id_venta');
    }
    
    // Relación con historial de compra
    public function historialCompra()
    {
        return $this->hasOne(Historial_Compra::class, 'id_venta');
    }
    
    // Generar folio único
    public static function generarFolio()
    {
        $fecha = now()->format('Ymd');
        $ultimaVenta = self::where('folio', 'like', "V{$fecha}%")->orderBy('id_venta', 'desc')->first();
        
        if ($ultimaVenta) {
            $numeroActual = intval(substr($ultimaVenta->folio, 9));
            $nuevoNumero = $numeroActual + 1;
        } else {
            $nuevoNumero = 1;
        }
        
        return "V{$fecha}" . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
    }
    
    // Calcular totales
    public function calcularTotales()
    {
        $subtotal = 0;
        
        foreach ($this->detalles as $detalle) {
            $subtotal += $detalle->subtotal;
        }
        
        $this->subtotal = $subtotal;
        
        // Aplicar descuento por membresía si hay cliente
        if ($this->cliente) {
            $membresiaActual = $this->cliente->membresiaActual();
            if ($membresiaActual) {
                $this->descuento = $subtotal * ($membresiaActual->membresia->porcentaje_descuento / 100);
            } else {
                $this->descuento = 0;
            }
        } else {
            $this->descuento = 0;
        }
        
        // Calcular impuesto (IVA 16%)
        $this->impuesto = ($subtotal - $this->descuento) * 0.16;
        
        // Total
        $this->total = $subtotal - $this->descuento + $this->impuesto;
        
        $this->save();
    }
}