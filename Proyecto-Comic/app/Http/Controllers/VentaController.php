<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Detalle_venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Historial_Compra;
use App\Models\Moneda;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $ventas = Venta::with(['cliente.persona', 'usuario.persona', 'moneda'])
                       ->orderBy('created_at', 'desc')
                       ->paginate(15);
        
        return view('ventas.index', compact('ventas'));
    }
    
    public function create()
    {
        $monedas = Moneda::all();
        return view('ventas.create', compact('monedas'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'id_moneda' => 'required|exists:monedas,id_moneda',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Crear venta
            $venta = Venta::create([
                'folio' => Venta::generarFolio(),
                'id_cliente' => $request->id_cliente,
                'id_usuario' => auth()->user()->usuario->id_usuario,
                'subtotal' => 0, // Se calculará después
                'descuento' => 0, // Se calculará después
                'impuesto' => 0, // Se calculará después
                'total' => 0, // Se calculará después
                'id_moneda' => $request->id_moneda,
                'metodo_pago' => $request->metodo_pago,
                'estatus' => 'completada'
            ]);
            
            $totalPuntos = 0;
            
            // Agregar detalles
            foreach ($request->productos as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                
                // Verificar stock
                if ($producto->stock_actual < $productoData['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto {$producto->nombre}");
                }
                
                // Crear detalle
                $detalle = Detalle_venta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $producto->id_producto,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio'],
                    'descuento' => 0, // Sin descuento individual
                    'subtotal' => $productoData['precio'] * $productoData['cantidad']
                ]);
                
                // Actualizar stock
                $producto->actualizarStock($productoData['cantidad'], 'salida');
                
                // Acumular puntos (1 punto por cada $100 de compra)
                $totalPuntos += floor(($detalle->subtotal / 100));
            }
            
            // Calcular totales de la venta
            $venta->calcularTotales();
            
            // Si hay cliente, registrar historial y puntos
            if ($venta->id_cliente) {
                // Registrar historial
                Historial_Compra::create([
                    'id_cliente' => $venta->id_cliente,
                    'id_venta' => $venta->id_venta,
                    'puntos_ganados' => $totalPuntos
                ]);
                
                // Sumar puntos al cliente
                $cliente = Cliente::find($venta->id_cliente);
                $cliente->sumarPuntos($totalPuntos);
            }
            
            // Registrar movimiento
            \App\Models\Movimiento::registrar(
                auth()->user()->usuario->id_usuario,
                'crear',
                'ventas',
                $venta->id_venta,
                null,
                "Creación de venta con folio {$venta->folio}"
            );
            
            DB::commit();
            
            return redirect()->route('ventas.show', $venta->id_venta)
                            ->with('success', 'Venta registrada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $venta = Venta::with([
            'cliente.persona', 
            'usuario.persona', 
            'moneda',
            'detalles.producto'
        ])->findOrFail($id);
        
        return view('ventas.show', compact('venta'));
    }
    
    public function cancelar($id)
    {
        $venta = Venta::findOrFail($id);
        
        if ($venta->estatus == 'cancelada') {
            return back()->with('error', 'La venta ya está cancelada.');
        }
        
        DB::beginTransaction();
        
        try {
            // Cambiar estatus
            $venta->estatus = 'cancelada';
            $venta->save();
            
            // Devolver stock de productos
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                $producto->actualizarStock($detalle->cantidad, 'entrada');
            }
            
            // Si hay cliente, restar puntos
            if ($venta->id_cliente) {
                $historialCompra = $venta->historialCompra;
                
                if ($historialCompra) {
                    $cliente = $venta->cliente;
                    $cliente->puntos_acumulados -= $historialCompra->puntos_ganados;
                    
                    if ($cliente->puntos_acumulados < 0) {
                        $cliente->puntos_acumulados = 0;
                    }
                    
                    $cliente->save();
                    
                    // Eliminar historial
                    $historialCompra->delete();
                }
            }
            
            // Registrar movimiento
            \App\Models\Movimiento::registrar(
                auth()->user()->usuario->id_usuario,
                'cancelar',
                'ventas',
                $venta->id_venta,
                null,
                "Cancelación de venta con folio {$venta->folio}"
            );
            
            DB::commit();
            
            return redirect()->route('ventas.show', $venta->id_venta)
                            ->with('success', 'Venta cancelada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al cancelar la venta: ' . $e->getMessage()]);
        }
    }
    
    public function imprimir($id)
    {
        $venta = Venta::with([
            'cliente.persona', 
            'usuario.persona', 
            'moneda',
            'detalles.producto'
        ])->findOrFail($id);
        
        return view('ventas.imprimir', compact('venta'));
    }
}