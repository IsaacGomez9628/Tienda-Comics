<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Persona;
use App\Models\Membresia;
use App\Models\Cliente_membresia;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Routing\Controller;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $clientes = Cliente::with(['persona', 'membresias'])->where('id_estatus', 1)->get();
        return view('clientes.index', compact('clientes'));
    }
    
    public function create()
    {
        $membresias = Membresia::where('id_estatus', 1)->orderBy('puntos_requeridos')->get();
        return view('clientes.create', compact('membresias'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:15',
            'fecha_nacimiento' => 'nullable|date',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Crear persona
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'id_estatus' => 1
            ]);
            
            // Generar código único para el cliente
            $codigo = 'CLI-' . strtoupper(Str::random(6));
            
            // Crear cliente
            $cliente = Cliente::create([
                'id_persona' => $persona->id_persona,
                'codigo_cliente' => $codigo,
                'puntos_acumulados' => 0,
                'id_estatus' => 1
            ]);
            
            // Asignar membresía básica (la de menos puntos)
            $membresiaBasica = Membresia::where('id_estatus', 1)
                                ->orderBy('puntos_requeridos')
                                ->first();
            
            if ($membresiaBasica) {
                Cliente_membresia::create([
                    'id_cliente' => $cliente->id_cliente,
                    'id_membresia' => $membresiaBasica->id_membresia,
                    'fecha_inicio' => now(),
                    'fecha_fin' => null,
                    'id_estatus' => 1
                ]);
            }
            
            // Registrar movimiento
            \App\Models\Movimiento::registrar(
                Auth::user()->id_usuario ?? Auth::id(),
                'crear',
                'clientes',
                $cliente->id_cliente,
                null,
                "Creación de cliente {$persona->nombreCompleto()}"
            );
            
            DB::commit();
            
            return redirect()->route('clientes.index')
                            ->with('success', 'Cliente creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el cliente: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $cliente = Cliente::with(['persona', 'membresias.membresia', 'ventas'])
                    ->findOrFail($id);
        
        $historialCompras = $cliente->historialCompras()
                            ->with('venta.detalles.producto')
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        return view('clientes.show', compact('cliente', 'historialCompras'));
    }
    
    public function edit($id)
    {
        $cliente = Cliente::with('persona')->findOrFail($id);
        $membresias = Membresia::where('id_estatus', 1)->orderBy('puntos_requeridos')->get();
        $membresiaActual = $cliente->membresiaActual();
        
        return view('clientes.edit', compact('cliente', 'membresias', 'membresiaActual'));
    }
    
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:15',
            'fecha_nacimiento' => 'nullable|date',
            'puntos_acumulados' => 'required|integer|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Actualizar persona
            $persona = Persona::findOrFail($cliente->id_persona);
            $persona->update([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'email' => $request->email
            ]);
            
            // Actualizar puntos
            $puntosAnteriores = $cliente->puntos_acumulados;
            $cliente->update([
                'puntos_acumulados' => $request->puntos_acumulados
            ]);
            
            // Verificar si se actualizó la membresía
            if ($puntosAnteriores != $request->puntos_acumulados) {
                $cliente->verificarNivelMembresia();
            }
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::user()->usuario->id_usuario,
                'actualizar',
                'clientes',
                $cliente->id_cliente,
                null,
                "Actualización de cliente {$persona->nombreCompleto()}"
            );
            
            DB::commit();
            
            return redirect()->route('clientes.index')
                            ->with('success', 'Cliente actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el cliente: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Cambiar estatus a inactivo
            $cliente->update(['id_estatus' => 2]); // 2 = Inactivo
            
            $persona = Persona::findOrFail($cliente->id_persona);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::user()->usuario->id_usuario,
                'desactivar',
                'clientes',
                $cliente->id_cliente,
                null,
                "Desactivación de cliente {$persona->nombreCompleto()}"
            );
            
            DB::commit();
            
            return redirect()->route('clientes.index')
                            ->with('success', 'Cliente desactivado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al desactivar el cliente: ' . $e->getMessage()]);
        }
    }
    
    public function buscar(Request $request)
    {
        $term = $request->input('term');
        
        $clientes = Cliente::whereHas('persona', function($query) use ($term) {
                        $query->where('nombre', 'like', "%{$term}%")
                              ->orWhere('apellido_paterno', 'like', "%{$term}%")
                              ->orWhere('apellido_materno', 'like', "%{$term}%");
                    })
                    ->orWhere('codigo_cliente', 'like', "%{$term}%")
                    ->where('id_estatus', 1)
                    ->with('persona')
                    ->limit(10)
                    ->get();
        
        $results = $clientes->map(function($cliente) {
            return [
                'id' => $cliente->id_cliente,
                'text' => $cliente->codigo_cliente . ' - ' . $cliente->persona->nombreCompleto()
            ];
        });
        
        return response()->json(['results' => $results]);
    }
}