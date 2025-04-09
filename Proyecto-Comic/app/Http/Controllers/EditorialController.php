<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Editorial;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Routing\Controller;

class EditorialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['index', 'show']);
    }
    
    public function index()
    {
        $editoriales = Editorial::where('id_estatus', 1)
                              ->withCount('productos')
                              ->orderBy('nombre')
                              ->get();
        
        return view('editoriales.index', compact('editoriales'));
    }
    
    public function create()
    {
        return view('editoriales.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:editoriales,nombre',
            'anio_fundacion' => 'nullable|integer|min:1800|max:' . date('Y'),
            'descripcion' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $editorial = Editorial::create([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'anio_fundacion' => $request->anio_fundacion,
                'descripcion' => $request->descripcion,
                'id_estatus' => 1
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'crear',
                'editoriales',
                $editorial->id_editorial,
                null,
                "Creación de editorial {$editorial->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('editoriales.index')
                            ->with('success', 'Editorial creada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la editorial: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $editorial = Editorial::withCount('productos')->findOrFail($id);
        
        $productos = \App\Models\Producto::where('id_editorial', $id)
                                      ->where('id_estatus', 1)
                                      ->with(['categoria'])
                                      ->paginate(10);
        
        return view('editoriales.show', compact('editorial', 'productos'));
    }
    
    public function edit($id)
    {
        $editorial = Editorial::findOrFail($id);
        return view('editoriales.edit', compact('editorial'));
    }
    
    public function update(Request $request, $id)
    {
        $editorial = Editorial::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:100|unique:editoriales,nombre,' . $id . ',id_editorial',
            'anio_fundacion' => 'nullable|integer|min:1800|max:' . date('Y'),
            'descripcion' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            $editorial->update([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'anio_fundacion' => $request->anio_fundacion,
                'descripcion' => $request->descripcion
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'actualizar',
                'editoriales',
                $editorial->id_editorial,
                null,
                "Actualización de editorial {$editorial->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('editoriales.index')
                            ->with('success', 'Editorial actualizada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la editorial: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function destroy($id)
    {
        $editorial = Editorial::findOrFail($id);
        
        // Verificar si tiene productos asociados
        $tieneProductos = \App\Models\Producto::where('id_editorial', $id)
                                           ->where('id_estatus', 1)
                                           ->exists();
        
        if ($tieneProductos) {
            return back()->with('error', 'No se puede desactivar una editorial con productos asociados.');
        }
        
        DB::beginTransaction();
        
        try {
            $editorial->update(['id_estatus' => 2]); // 2 = Inactivo
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'desactivar',
                'editoriales',
                $editorial->id_editorial,
                null,
                "Desactivación de editorial {$editorial->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('editoriales.index')
                            ->with('success', 'Editorial desactivada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al desactivar la editorial: ' . $e->getMessage()]);
        }
    }
}