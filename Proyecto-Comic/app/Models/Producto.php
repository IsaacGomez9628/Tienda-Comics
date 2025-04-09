<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    
    protected $fillable = [
        'codigo_barras',
        'nombre',
        'slug',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'id_moneda',
        'id_categoria',
        'id_editorial',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'tipo_producto',
        'id_estatus'
    ];
    
    // Relación con moneda
    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda');
    }
    
    // Relación con categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }
    
    // Relación con editorial
    public function editorial()
    {
        return $this->belongsTo(Editorial::class, 'id_editorial');
    }
    
    // Relación con detalles de venta
    public function detallesVenta()
    {
        return $this->hasMany(Detalle_venta::class, 'id_producto');
    }
    
    // Relación con detalles de pedido
    public function detallesPedido()
    {
        return $this->hasMany(Detalle_pedido::class, 'id_producto');
    }
    
    // Relación con proveedores
    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'producto_proveedores', 'id_producto', 'id_proveedor')
                    ->withPivot('es_proveedor_principal', 'precio_proveedor', 'tiempo_entrega_dias', 'notas')
                    ->withTimestamps();
    }
    
    // Proveedor principal
    public function proveedorPrincipal()
    {
        return $this->proveedores()->wherePivot('es_proveedor_principal', true)->first();
    }
    
    // Relación con comic
    public function comic()
    {
        return $this->hasOne(Comic::class, 'id_producto');
    }
    
    // Relación con figura
    public function figura()
    {
        return $this->hasOne(Figura::class, 'id_producto');
    }
    
    // Verificar si el producto necesita reabastecimiento
    public function necesitaReabastecimiento()
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
    
    // Actualizar stock
    public function actualizarStock($cantidad, $tipo)
    {
        if ($tipo === 'entrada') {
            $this->stock_actual += $cantidad;
        } else {
            $this->stock_actual -= $cantidad;
        }
        
        $this->save();
        return $this->stock_actual;
    }
}