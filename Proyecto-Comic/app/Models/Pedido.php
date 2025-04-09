<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    
    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';
    
    protected $fillable = [
        'folio',
        'id_proveedor',
        'id_usuario',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'subtotal',
        'impuesto',
        'total',
        'id_moneda',
        'id_estado_pedido',
        'notas'
    ];
    
    protected $dates = [
        'fecha_entrega_estimada',
        'fecha_entrega_real'
    ];
    
    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
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
    
    // Relación con estado de pedido
    public function estadoPedido()
    {
        return $this->belongsTo(Estado_pedido::class, 'id_estado_pedido');
    }
    
    // Relación con detalles de pedido
    public function detalles()
    {
        return $this->hasMany(Detalle_pedido::class, 'id_pedido');
    }
    
    // Relación con comunicaciones con el proveedor
    public function comunicaciones()
    {
        return $this->hasMany(Comunicacion_proveedor::class, 'id_pedido');
    }
    
    // Relación con imágenes
    public function imagenes()
    {
        return $this->morphMany(Imagen::class, 'imageable');
    }
    
    // Generar folio único
    public static function generarFolio()
    {
        $fecha = now()->format('Ymd');
        $ultimoPedido = self::where('folio', 'like', "P{$fecha}%")->orderBy('id_pedido', 'desc')->first();
        
        if ($ultimoPedido) {
            $numeroActual = intval(substr($ultimoPedido->folio, 9));
            $nuevoNumero = $numeroActual + 1;
        } else {
            $nuevoNumero = 1;
        }
        
        return "P{$fecha}" . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
    }
    
    // Calcular totales
    public function calcularTotales()
    {
        $subtotal = 0;
        
        foreach ($this->detalles as $detalle) {
            $subtotal += $detalle->subtotal;
        }
        
        $this->subtotal = $subtotal;
        
        // Calcular impuesto (IVA 16%)
        $this->impuesto = $subtotal * 0.16;
        
        // Total
        $this->total = $subtotal + $this->impuesto;
        
        $this->save();
    }
    
    // Cambiar estado del pedido
    public function cambiarEstado($idEstado)
    {
        $this->id_estado_pedido = $idEstado;
        $this->save();
        
        // Obtener el ID del usuario actual de forma segura
        $usuarioId = auth()->id();
        
        // Registrar movimiento
        Movimiento::registrar(
            $usuarioId,
            'cambio_estado_pedido',
            'pedidos',
            $this->id_pedido,
            null,
            "Cambio de estado de pedido a: " . Estado_pedido::find($idEstado)->nombre
        );
        
        return $this->estadoPedido;
    }
}