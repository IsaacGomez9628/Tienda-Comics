<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        // Obtener ventas del día actual
        $hoy = Carbon::today();
        $ventasHoy = Venta::whereDate('created_at', $hoy)
                         ->where('estatus', 'completada')
                         ->sum('total');
        
        // Obtener ventas del mes actual
        $inicioMes = Carbon::today()->startOfMonth();
        $finMes = Carbon::today()->endOfMonth();
        $ventasMes = Venta::whereBetween('created_at', [$inicioMes, $finMes])
                         ->where('estatus', 'completada')
                         ->sum('total');
        
        // Productos con bajo stock
        $productosBajoStock = Producto::whereRaw('stock_actual <= stock_minimo')
                                    ->where('id_estatus', 1)
                                    ->with(['categoria', 'editorial'])
                                    ->limit(5)
                                    ->get();
        
        // Ventas recientes
        $ventasRecientes = Venta::with(['cliente.persona', 'usuario.persona'])
                              ->orderBy('created_at', 'desc')
                              ->limit(5)
                              ->get();
        
        // Pedidos pendientes
        $pedidosPendientes = Pedido::whereIn('id_estado_pedido', [1, 2, 4]) // Pendiente, Enviado, Confirmado
                                 ->with(['proveedor', 'estadoPedido'])
                                 ->orderBy('created_at', 'desc')
                                 ->limit(5)
                                 ->get();
        
        // Clientes nuevos este mes
        $clientesNuevosMes = Cliente::whereBetween('created_at', [$inicioMes, $finMes])
                                  ->count();
        
        // Productos más vendidos del mes
        $productosPopulares = DB::table('detalle_ventas')
                                ->join('ventas', 'detalle_ventas.id_venta', '=', 'ventas.id_venta')
                                ->join('productos', 'detalle_ventas.id_producto', '=', 'productos.id_producto')
                                ->select(
                                    'productos.id_producto',
                                    'productos.nombre',
                                    DB::raw('SUM(detalle_ventas.cantidad) as total_vendido')
                                )
                                ->where('ventas.estatus', 'completada')
                                ->whereBetween('ventas.created_at', [$inicioMes, $finMes])
                                ->groupBy('productos.id_producto', 'productos.nombre')
                                ->orderBy('total_vendido', 'desc')
                                ->limit(5)
                                ->get();
        
        // Datos para gráfico de ventas por día (últimos 7 días)
        $ventasUltimos7Dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $totalDia = Venta::whereDate('created_at', $fecha)
                           ->where('estatus', 'completada')
                           ->sum('total');
            
            $ventasUltimos7Dias[] = [
                'fecha' => $fecha->format('d/m'),
                'total' => $totalDia
            ];
        }
        
        return view('dashboard', compact(
            'ventasHoy',
            'ventasMes',
            'productosBajoStock',
            'ventasRecientes',
            'pedidosPendientes',
            'clientesNuevosMes',
            'productosPopulares',
            'ventasUltimos7Dias'
        ));
    }
    
    public function reporteVentas(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? Carbon::today()->startOfMonth()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? Carbon::today()->format('Y-m-d');
        
        $ventas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin . ' 23:59:59'])
                      ->with(['cliente.persona', 'detalles.producto'])
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        $totalVentas = $ventas->where('estatus', 'completada')->sum('total');
        $promedioVenta = $ventas->where('estatus', 'completada')->avg('total') ?? 0;
        $cantidadVentas = $ventas->where('estatus', 'completada')->count();
        $ventasCanceladas = $ventas->where('estatus', 'cancelada')->count();
        
        // Ventas por categoría
        $ventasPorCategoria = [];
        foreach ($ventas->where('estatus', 'completada') as $venta) {
            foreach ($venta->detalles as $detalle) {
                $categoria = $detalle->producto->categoria->nombre;
                $subtotal = $detalle->subtotal;
                
                if (!isset($ventasPorCategoria[$categoria])) {
                    $ventasPorCategoria[$categoria] = 0;
                }
                
                $ventasPorCategoria[$categoria] += $subtotal;
            }
        }
        
        // Ordenar por valor descendente
        arsort($ventasPorCategoria);
        
        return view('reportes.ventas', compact(
            'ventas', 
            'fechaInicio', 
            'fechaFin', 
            'totalVentas', 
            'promedioVenta', 
            'cantidadVentas', 
            'ventasCanceladas',
            'ventasPorCategoria'
        ));
    }
    
    public function reporteInventario()
    {
        $productos = Producto::with(['categoria', 'editorial'])
                           ->where('id_estatus', 1)
                           ->get();
        
        $totalProductos = $productos->count();
        $valorInventario = $productos->sum(function($producto) {
            return $producto->precio_compra * $producto->stock_actual;
        });
        
        $productosSinStock = $productos->where('stock_actual', 0)->count();
        $productosBajoStock = $productos->where('stock_actual', '>', 0)
                                      ->where('stock_actual', '<=', DB::raw('stock_minimo'))
                                      ->count();
        
        // Productos por categoría
        $productosPorCategoria = [];
        foreach ($productos as $producto) {
            $categoria = $producto->categoria->nombre;
            
            if (!isset($productosPorCategoria[$categoria])) {
                $productosPorCategoria[$categoria] = 0;
            }
            
            $productosPorCategoria[$categoria]++;
        }
        
        // Ordenar por cantidad descendente
        arsort($productosPorCategoria);
        
        return view('reportes.inventario', compact(
            'productos', 
            'totalProductos', 
            'valorInventario', 
            'productosSinStock', 
            'productosBajoStock',
            'productosPorCategoria'
        ));
    }
    
    public function reporteClientes()
    {
        $clientes = Cliente::with(['persona', 'membresias.membresia'])
                         ->where('id_estatus', 1)
                         ->get();
        
        $totalClientes = $clientes->count();
        
        // Clientes por membresía
        $clientesPorMembresia = [];
        foreach ($clientes as $cliente) {
            $membresiaActual = $cliente->membresiaActual();
            $nombreMembresia = $membresiaActual ? $membresiaActual->membresia->nombre : 'Sin membresía';
            
            if (!isset($clientesPorMembresia[$nombreMembresia])) {
                $clientesPorMembresia[$nombreMembresia] = 0;
            }
            
            $clientesPorMembresia[$nombreMembresia]++;
        }
        
        // Clientes con más compras (top 10)
        $clientesTopCompras = Cliente::withCount(['ventas' => function($query) {
                                     $query->where('estatus', 'completada');
                                 }])
                                ->with('persona')
                                ->where('id_estatus', 1)
                                ->orderBy('ventas_count', 'desc')
                                ->limit(10)
                                ->get();
        
        return view('reportes.clientes', compact(
            'clientes', 
            'totalClientes', 
            'clientesPorMembresia',
            'clientesTopCompras'
        ));
    }
}