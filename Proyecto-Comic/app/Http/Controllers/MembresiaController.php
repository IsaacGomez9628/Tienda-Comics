<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membresia;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;

class MembresiaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    public function index()
    {
        $membresias = Membresia::where('id_estatus', 1)
                              ->orderBy('puntos_requeridos')
                              ->get();
        
        return view('membresias.index', compact('membresias'));
    }
    
    public function create()
    {
        return view('membresias.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:membresias,nombre',
            'descripcion' => 'nullable|string',
            'puntos_requeridos' => 'required|integer|min:0',
            'porcentaje_descuento' => 'required|numeric|min:0|max:100'
        ]);
        
        DB::beginTransaction();
        
        try {
            $membresia = Membresia::create([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'puntos_requeridos' => $request->puntos_requeridos,
                'porcentaje_descuento' => $request->porcentaje_descuento,
                'id_estatus' => 1
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'crear',
                'membresias',
                $membresia->id_membresia,
                null,
                "Creación de membresía {$membresia->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('membresias.index')
                            ->with('success', 'Membresía creada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la membresía: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $membresia = Membresia::findOrFail($id);
        $clientesActivos = $membresia->clienteMembresias()
                                    ->where(function($query) {
                                        $query->whereNull('fecha_fin')
                                              ->orWhere('fecha_fin', '>=', now());
                                    })
                                    ->where('id_estatus', 1)
                                    ->with('cliente.persona')
                                    ->get();
        
        return view('membresias.show', compact('membresia', 'clientesActivos'));
    }
    
    public function edit($id)
    {
        $membresia = Membresia::findOrFail($id);
        return view('membresias.edit', compact('membresia'));
    }
    
    public function update(Request $request, $id)
    {
        $membresia = Membresia::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:50|unique:membresias,nombre,' . $id . ',id_membresia',
            'descripcion' => 'nullable|string',
            'puntos_requeridos' => 'required|integer|min:0',
            'porcentaje_descuento' => 'required|numeric|min:0|max:100'
        ]);
        
        DB::beginTransaction();
        
        try {
            $membresia->update([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'puntos_requeridos' => $request->puntos_requeridos,
                'porcentaje_descuento' => $request->porcentaje_descuento
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'actualizar',
                'membresias',
                $membresia->id_membresia,
                null,
                "Actualización de membresía {$membresia->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('membresias.index')
                            ->with('success', 'Membresía actualizada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la membresía: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function destroy($id)
    {
        $membresia = Membresia::findOrFail($id);
        
        // Verificar si es la membresía básica (la de menor puntos)
        $membresiaBasica = Membresia::where('id_estatus', 1)
                                   ->orderBy('puntos_requeridos')
                                   ->first();
        
        if ($membresia->id_membresia == $membresiaBasica->id_membresia) {
            return back()->with('error', 'No puedes desactivar la membresía básica.');
        }
        
        DB::beginTransaction();
        
        try {
            $membresia->update(['id_estatus' => 2]); // 2 = Inactivo
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'desactivar',
                'membresias',
                $membresia->id_membresia,
                null,
                "Desactivación de membresía {$membresia->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('membresias.index')
                            ->with('success', 'Membresía desactivada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al desactivar la membresía: ' . $e->getMessage()]);
        }
    }
}