<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Persona;
use App\Models\Direccion;
use App\Models\Codigo_postal;
use App\Models\Ciudad;
use App\Models\Estado;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['index', 'show']);
    }
    
    public function index()
    {
        $proveedores = Proveedor::where('id_estatus', 1)
                              ->orderBy('nombre_empresa')
                              ->get();
        
        return view('proveedores.index', compact('proveedores'));
    }
    
    public function create()
    {
        $estados = Estado::where('id_estatus', 1)->orderBy('nombre')->get();
        return view('proveedores.create', compact('estados'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre_empresa' => 'required|string|max:100',
            'nombre_contacto' => 'nullable|string|max:100',
            'apellido_paterno_contacto' => 'nullable|string|max:100',
            'apellido_materno_contacto' => 'nullable|string|max:100',
            'telefono' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'pagina_web' => 'nullable|url|max:100',
            'notas' => 'nullable|string',
            
            // Dirección
            'calle' => 'nullable|string|max:100',
            'numero_exterior' => 'nullable|string|max:20',
            'numero_interior' => 'nullable|string|max:20',
            'colonia' => 'nullable|string|max:100',
            'id_estado' => 'nullable|exists:estados,id_estado',
            'id_ciudad' => 'nullable|exists:ciudades,id_ciudad',
            'codigo_postal' => 'nullable|string|max:10',
            'referencias' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Crear dirección si se proporcionaron datos
            $idDireccion = null;
            
            if ($request->filled('calle') && $request->filled('id_ciudad')) {
                // Buscar o crear código postal
                $codigoPostal = Codigo_postal::firstOrCreate(
                    ['codigo' => $request->codigo_postal, 'id_ciudad' => $request->id_ciudad],
                    ['id_estatus' => 1]
                );
                
                // Crear dirección
                $direccion = Direccion::create([
                    'calle' => $request->calle,
                    'numero_exterior' => $request->numero_exterior,
                    'numero_interior' => $request->numero_interior,
                    'colonia' => $request->colonia,
                    'id_codigo_postal' => $codigoPostal->id_codigo_postal,
                    'referencias' => $request->referencias,
                    'id_estatus' => 1
                ]);
                
                $idDireccion = $direccion->id_direccion;
            }
            
            // Crear persona de contacto si se proporcionaron datos
            $idPersonaContacto = null;
            
            if ($request->filled('nombre_contacto')) {
                $persona = Persona::create([
                    'nombre' => $request->nombre_contacto,
                    'apellido_paterno' => $request->apellido_paterno_contacto,
                    'apellido_materno' => $request->apellido_materno_contacto,
                    'telefono' => $request->telefono,
                    'email' => $request->email,
                    'id_direccion' => $idDireccion,
                    'id_estatus' => 1
                ]);
                
                $idPersonaContacto = $persona->id_persona;
            }
            
            // Crear proveedor
            $proveedor = Proveedor::create([
                'nombre_empresa' => $request->nombre_empresa,
                'slug' => Str::slug($request->nombre_empresa),
                'id_persona_contacto' => $idPersonaContacto,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'id_direccion' => $idDireccion,
                'pagina_web' => $request->pagina_web,
                'notas' => $request->notas,
                'id_estatus' => 1
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'crear',
                'proveedores',
                $proveedor->id_proveedor,
                null,
                "Creación de proveedor {$proveedor->nombre_empresa}"
            );
            
            DB::commit();
            
            return redirect()->route('proveedores.index')
                            ->with('success', 'Proveedor creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el proveedor: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $proveedor = Proveedor::with([
            'personaContacto',
            'direccion.codigoPostal.ciudad.estado'
        ])->findOrFail($id);
        
        $productos = Producto::whereHas('proveedores', function($query) use ($id) {
                         $query->where('id_proveedor', $id);
                    })
                    ->where('id_estatus', 1)
                    ->paginate(10);
        
        $pedidos = $proveedor->pedidos()
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
        
        return view('proveedores.show', compact('proveedor', 'productos', 'pedidos'));
    }
    
    public function edit($id)
    {
        $proveedor = Proveedor::with([
            'personaContacto',
            'direccion.codigoPostal.ciudad.estado'
        ])->findOrFail($id);
        
        $estados = Estado::where('id_estatus', 1)->orderBy('nombre')->get();
        
        // Si tiene dirección, cargar ciudades del estado correspondiente
        $ciudades = collect();
        if ($proveedor->direccion && $proveedor->direccion->codigoPostal) {
            $idEstado = $proveedor->direccion->codigoPostal->ciudad->estado->id_estado;
            $ciudades = Ciudad::where('id_estado', $idEstado)
                            ->where('id_estatus', 1)
                            ->orderBy('nombre')
                            ->get();
        }
        
        return view('proveedores.edit', compact('proveedor', 'estados', 'ciudades'));
    }
    
    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        $request->validate([
            'nombre_empresa' => 'required|string|max:100',
            'nombre_contacto' => 'nullable|string|max:100',
            'apellido_paterno_contacto' => 'nullable|string|max:100',
            'apellido_materno_contacto' => 'nullable|string|max:100',
            'telefono' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'pagina_web' => 'nullable|url|max:100',
            'notas' => 'nullable|string',
            
            // Dirección
            'calle' => 'nullable|string|max:100',
            'numero_exterior' => 'nullable|string|max:20',
            'numero_interior' => 'nullable|string|max:20',
            'colonia' => 'nullable|string|max:100',
            'id_estado' => 'nullable|exists:estados,id_estado',
            'id_ciudad' => 'nullable|exists:ciudades,id_ciudad',
            'codigo_postal' => 'nullable|string|max:10',
            'referencias' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Actualizar dirección si se proporcionaron datos
            if ($request->filled('calle') && $request->filled('id_ciudad')) {
                // Buscar o crear código postal
                $codigoPostal = Codigo_postal::firstOrCreate(
                    ['codigo' => $request->codigo_postal, 'id_ciudad' => $request->id_ciudad],
                    ['id_estatus' => 1]
                );
                
                if ($proveedor->id_direccion) {
                    // Actualizar dirección existente
                    $direccion = Direccion::findOrFail($proveedor->id_direccion);
                    $direccion->update([
                        'calle' => $request->calle,
                        'numero_exterior' => $request->numero_exterior,
                        'numero_interior' => $request->numero_interior,
                        'colonia' => $request->colonia,
                        'id_codigo_postal' => $codigoPostal->id_codigo_postal,
                        'referencias' => $request->referencias
                    ]);
                } else {
                    // Crear nueva dirección
                    $direccion = Direccion::create([
                        'calle' => $request->calle,
                        'numero_exterior' => $request->numero_exterior,
                        'numero_interior' => $request->numero_interior,
                        'colonia' => $request->colonia,
                        'id_codigo_postal' => $codigoPostal->id_codigo_postal,
                        'referencias' => $request->referencias,
                        'id_estatus' => 1
                    ]);
                    
                    $proveedor->id_direccion = $direccion->id_direccion;
                }
            }
            
            // Actualizar persona de contacto
            if ($proveedor->id_persona_contacto) {
                $persona = Persona::findOrFail($proveedor->id_persona_contacto);
                $persona->update([
                    'nombre' => $request->nombre_contacto,
                    'apellido_paterno' => $request->apellido_paterno_contacto,
                    'apellido_materno' => $request->apellido_materno_contacto,
                    'telefono' => $request->telefono,
                    'email' => $request->email
                ]);
            } else if ($request->filled('nombre_contacto')) {
                // Crear nueva persona de contacto
                $persona = Persona::create([
                    'nombre' => $request->nombre_contacto,
                    'apellido_paterno' => $request->apellido_paterno_contacto,
                    'apellido_materno' => $request->apellido_materno_contacto,
                    'telefono' => $request->telefono,
                    'email' => $request->email,
                    'id_direccion' => $proveedor->id_direccion,
                    'id_estatus' => 1
                ]);
                
                $proveedor->id_persona_contacto = $persona->id_persona;
            }
            
            // Actualizar proveedor
            $proveedor->update([
                'nombre_empresa' => $request->nombre_empresa,
                'slug' => Str::slug($request->nombre_empresa),
                'telefono' => $request->telefono,
                'email' => $request->email,
                'pagina_web' => $request->pagina_web,
                'notas' => $request->notas
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'actualizar',
                'proveedores',
                $proveedor->id_proveedor,
                null,
                "Actualización de proveedor {$proveedor->nombre_empresa}"
            );
            
            DB::commit();
            
            return redirect()->route('proveedores.index')
                            ->with('success', 'Proveedor actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el proveedor: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        // Verificar si tiene pedidos activos
        $tienePedidos = $proveedor->pedidos()
                                ->whereIn('id_estado_pedido', [1, 2, 4]) // Pendiente, Enviado, Confirmado
                                ->exists();
        
        if ($tienePedidos) {
            return back()->with('error', 'No se puede desactivar un proveedor con pedidos activos.');
        }
        
        DB::beginTransaction();
        
        try {
            $proveedor->update(['id_estatus' => 2]); // 2 = Inactivo
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'desactivar',
                'proveedores',
                $proveedor->id_proveedor,
                null,
                "Desactivación de proveedor {$proveedor->nombre_empresa}"
            );
            
            DB::commit();
            
            return redirect()->route('proveedores.index')
                            ->with('success', 'Proveedor desactivado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al desactivar el proveedor: ' . $e->getMessage()]);
        }
    }
    
    public function getCiudades($idEstado)
    {
        $ciudades = Ciudad::where('id_estado', $idEstado)
                        ->where('id_estatus', 1)
                        ->orderBy('nombre')
                        ->get();
        
        return response()->json($ciudades);
    }
}