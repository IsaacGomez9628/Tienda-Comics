<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_venta extends Model
{
    use HasFactory;
    
    protected $table = 'detalle_ventas';
    protected $primaryKey = 'id_detalle_venta';
    
    protected $fillable = [
        'id_venta',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal'
    ];
    
    // Relación con venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
    
    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    
    // Calcular subtotal
    public function calcularSubtotal()
    {
        $this->subtotal = ($this->precio_unitario * $this->cantidad) - $this->descuento;
        $this->save();
        return $this->subtotal;
    }
}