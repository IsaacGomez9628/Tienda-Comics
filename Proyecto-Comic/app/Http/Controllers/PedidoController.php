<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Detalle_pedido;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Estado_pedido;
use App\Models\Moneda;
use App\Models\Comunicacion_proveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PedidoProveedor;
use App\Models\Movimiento;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy', 'enviarCorreo']);
    }
    
    public function index()
    {
        $pedidos = Pedido::with(['proveedor', 'usuario.persona', 'estadoPedido'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
        
        return view('pedidos.index', compact('pedidos'));
    }
    
    public function create()
    {
        $proveedores = Proveedor::where('id_estatus', 1)->orderBy('nombre_empresa')->get();
        $monedas = Moneda::all();
        $estadosPedido = Estado_pedido::all();
        
        return view('pedidos.create', compact('proveedores', 'monedas', 'estadosPedido'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'id_moneda' => 'required|exists:monedas,id_moneda',
            'fecha_entrega_estimada' => 'nullable|date',
            'id_estado_pedido' => 'required|exists:estado_pedidos,id_estado_pedido',
            'notas' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Crear pedido
            $pedido = Pedido::create([
                'folio' => Pedido::generarFolio(),
                'id_proveedor' => $request->id_proveedor,
                'id_usuario' => Auth::id(),
                'fecha_entrega_estimada' => $request->fecha_entrega_estimada,
                'fecha_entrega_real' => null,
                'subtotal' => 0, // Se calculará después
                'impuesto' => 0, // Se calculará después
                'total' => 0, // Se calculará después
                'id_moneda' => $request->id_moneda,
                'id_estado_pedido' => $request->id_estado_pedido,
                'notas' => $request->notas
            ]);
            
            // Agregar detalles
            foreach ($request->productos as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                
                // Crear detalle
                Detalle_pedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $producto->id_producto,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio'],
                    'subtotal' => $productoData['precio'] * $productoData['cantidad']
                ]);
            }
            
            // Calcular totales del pedido
            $pedido->calcularTotales();
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'crear',
                'pedidos',
                $pedido->id_pedido,
                null,
                "Creación de pedido con folio {$pedido->folio}"
            );
            
            DB::commit();
            
            return redirect()->route('pedidos.show', $pedido->id_pedido)
                            ->with('success', 'Pedido registrado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el pedido: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $pedido = Pedido::with([
            'proveedor', 
            'usuario.persona', 
            'moneda',
            'estadoPedido',
            'detalles.producto',
            'comunicaciones'
        ])->findOrFail($id);
        
        return view('pedidos.show', compact('pedido'));
    }
    
    public function edit($id)
    {
        $pedido = Pedido::with([
            'proveedor', 
            'detalles.producto'
        ])->findOrFail($id);
        
        $proveedores = Proveedor::where('id_estatus', 1)->orderBy('nombre_empresa')->get();
        $monedas = Moneda::all();
        $estadosPedido = Estado_pedido::all();
        
        return view('pedidos.edit', compact('pedido', 'proveedores', 'monedas', 'estadosPedido'));
    }
    
    public function update(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        
        $request->validate([
            'fecha_entrega_estimada' => 'nullable|date',
            'fecha_entrega_real' => 'nullable|date',
            'id_estado_pedido' => 'required|exists:estado_pedidos,id_estado_pedido',
            'notas' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $estadoAnterior = $pedido->id_estado_pedido;
            
            // Actualizar pedido
            $pedido->update([
                'fecha_entrega_estimada' => $request->fecha_entrega_estimada,
                'fecha_entrega_real' => $request->fecha_entrega_real,
                'id_estado_pedido' => $request->id_estado_pedido,
                'notas' => $request->notas
            ]);
            
            // Si el estado cambió a recibido, aumentar stock
            if ($estadoAnterior != $request->id_estado_pedido && 
                $request->id_estado_pedido == 3) { // 3 = Recibido
                
                foreach ($pedido->detalles as $detalle) {
                    $detalle->producto->actualizarStock($detalle->cantidad, 'entrada');
                }
            }
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'actualizar',
                'pedidos',
                $pedido->id_pedido,
                null,
                "Actualización de pedido con folio {$pedido->folio}"
            );
            
            DB::commit();
            
            return redirect()->route('pedidos.show', $pedido->id_pedido)
                            ->with('success', 'Pedido actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el pedido: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function enviarCorreo($id)
    {
        $pedido = Pedido::with([
            'proveedor', 
            'detalles.producto',
            'moneda'
        ])->findOrFail($id);
        
        if (!$pedido->proveedor->email) {
            return back()->with('error', 'El proveedor no tiene un correo electrónico registrado.');
        }
        
        DB::beginTransaction();
        
        try {
            // Registrar comunicación
            $comunicacion = Comunicacion_proveedor::create([
                'id_pedido' => $pedido->id_pedido,
                'asunto' => "Pedido {$pedido->folio}",
                'contenido' => "Se ha generado un nuevo pedido con folio {$pedido->folio}.",
                'email_destino' => $pedido->proveedor->email,
                'email_cc' => null,
                'estatus' => 'enviado'
            ]);
            
            // Enviar correo
            Mail::to($pedido->proveedor->email)
                ->send(new PedidoProveedor($pedido));
            
            // Cambiar estado a enviado si está en pendiente
            if ($pedido->id_estado_pedido == 1) { // 1 = Pendiente
                $pedido->cambiarEstado(2); // 2 = Enviado
            }
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'comunicar',
                'pedidos',
                $pedido->id_pedido,
                null,
                "Envío de correo a proveedor para pedido {$pedido->folio}"
            );
            
            DB::commit();
            
            return redirect()->route('pedidos.show', $pedido->id_pedido)
                            ->with('success', 'Correo enviado correctamente al proveedor');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al enviar el correo: ' . $e->getMessage()]);
        }
    }
    
    public function registrarRespuesta(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);
        
        $request->validate([
            'id_comunicacion' => 'required|exists:comunicacion_proveedores,id_comunicacion',
            'respuesta' => 'required|string',
            'estatus' => 'required|in:recibido,confirmado,rechazado'
        ]);
        
        DB::beginTransaction();
        
        try {
            $comunicacion = Comunicacion_proveedor::findOrFail($request->id_comunicacion);
            
            // Actualizar comunicación
            $comunicacion->update([
                'respuesta' => $request->respuesta,
                'estatus' => $request->estatus,
                'fecha_respuesta' => now()
            ]);
            
            // Actualizar estado del pedido según la respuesta
            if ($request->estatus == 'confirmado') {
                $pedido->cambiarEstado(4); // 4 = Confirmado
            } else if ($request->estatus == 'rechazado') {
                $pedido->cambiarEstado(5); // 5 = Rechazado
            }
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'actualizar',
                'comunicacion_proveedores',
                $comunicacion->id_comunicacion,
                null,
                "Registro de respuesta de proveedor para pedido {$pedido->folio}"
            );
            
            DB::commit();
            
            return redirect()->route('pedidos.show', $pedido->id_pedido)
                            ->with('success', 'Respuesta registrada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar la respuesta: ' . $e->getMessage()]);
        }
    }
    
    public function recibirPedido($id)
    {
        $pedido = Pedido::findOrFail($id);
        
        if ($pedido->id_estado_pedido == 3) { // 3 = Recibido
            return back()->with('error', 'El pedido ya está marcado como recibido.');
        }
        
        DB::beginTransaction();
        
        try {
            // Cambiar estado
            $pedido->cambiarEstado(3); // 3 = Recibido
            
            // Actualizar fecha de entrega real
            $pedido->fecha_entrega_real = now();
            $pedido->save();
            
            // Actualizar stock de productos
            foreach ($pedido->detalles as $detalle) {
                $detalle->producto->actualizarStock($detalle->cantidad, 'entrada');
            }
            
            DB::commit();
            
            return redirect()->route('pedidos.show', $pedido->id_pedido)
                            ->with('success', 'Pedido marcado como recibido correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al recibir el pedido: ' . $e->getMessage()]);
        }
    }
    
    public function cancelar($id)
    {
        $pedido = Pedido::findOrFail($id);
        
        if (in_array($pedido->id_estado_pedido, [3, 5])) { // 3 = Recibido, 5 = Cancelado
            return back()->with('error', 'No se puede cancelar un pedido que ya está recibido o cancelado.');
        }
        
        DB::beginTransaction();
        
        try {
            // Cambiar estado
            $pedido->cambiarEstado(5); // 5 = Cancelado
            
            DB::commit();
            
            return redirect()->route('pedidos.show', $pedido->id_pedido)
                            ->with('success', 'Pedido cancelado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al cancelar el pedido: ' . $e->getMessage()]);
        }
    }
}