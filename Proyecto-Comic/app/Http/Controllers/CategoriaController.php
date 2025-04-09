<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['index', 'show']);
    }
    
    public function index()
    {
        $categorias = Categoria::where('id_estatus', 1)
                             ->withCount('productos')
                             ->orderBy('nombre')
                             ->get();
        
        return view('categorias.index', compact('categorias'));
    }
    
    public function create()
    {
        $categorias = Categoria::where('id_estatus', 1)->orderBy('nombre')->get();
        return view('categorias.create', compact('categorias'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre',
            'descripcion' => 'nullable|string',
            'categoria_padre' => 'nullable|exists:categorias,id_categoria'
        ]);
        
        DB::beginTransaction();
        
        try {
            $categoria = Categoria::create([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'categoria_padre' => $request->categoria_padre,
                'id_estatus' => 1
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Usuario::where('id_user', Auth::id())->first()->id_usuario,
                'crear',
                'categorias',
                $categoria->id_categoria,
                null,
                "Creación de categoría {$categoria->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('categorias.index')
                            ->with('success', 'Categoría creada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la categoría: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $categoria = Categoria::with('categoriaPadre')
                           ->withCount('productos')
                           ->findOrFail($id);
        
        $subcategorias = Categoria::where('categoria_padre', $id)
                                ->where('id_estatus', 1)
                                ->withCount('productos')
                                ->get();
        
        $productos = \App\Models\Producto::where('id_categoria', $id)
                                      ->where('id_estatus', 1)
                                      ->with(['editorial'])
                                      ->paginate(10);
        
        return view('categorias.show', compact('categoria', 'subcategorias', 'productos'));
    }
    
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categorias = Categoria::where('id_estatus', 1)
                             ->where('id_categoria', '!=', $id)
                             ->whereNull('categoria_padre')
                             ->orderBy('nombre')
                             ->get();
        
        return view('categorias.edit', compact('categoria', 'categorias'));
    }
    
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:50|unique:categorias,nombre,' . $id . ',id_categoria',
            'descripcion' => 'nullable|string',
            'categoria_padre' => 'nullable|exists:categorias,id_categoria'
        ]);
        
        // Verificar que no se esté asignando a sí misma como padre
        if ($request->categoria_padre == $id) {
            return back()->withErrors(['categoria_padre' => 'No puedes asignar la categoría como su propio padre.'])
                         ->withInput();
        }
        
        // Verificar que no se esté creando un ciclo en la jerarquía
        if ($request->categoria_padre) {
            $padreTemp = Categoria::find($request->categoria_padre);
            while ($padreTemp && $padreTemp->categoria_padre) {
                if ($padreTemp->categoria_padre == $id) {
                    return back()->withErrors(['categoria_padre' => 'No puedes asignar como padre una categoría que es descendiente de esta categoría.'])
                                 ->withInput();
                }
                $padreTemp = Categoria::find($padreTemp->categoria_padre);
            }
        }
        
        DB::beginTransaction();
        
        try {
            $categoria->update([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'categoria_padre' => $request->categoria_padre
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::id(),
                'actualizar',
                'categorias',
                $categoria->id_categoria,
                null,
                "Actualización de categoría {$categoria->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('categorias.index')
                            ->with('success', 'Categoría actualizada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la categoría: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        
        // Verificar si tiene productos asociados
        $tieneProductos = \App\Models\Producto::where('id_categoria', $id)
                                           ->where('id_estatus', 1)
                                           ->exists();
        
        if ($tieneProductos) {
            return back()->with('error', 'No se puede desactivar una categoría con productos asociados.');
        }
        
        // Verificar si tiene subcategorías
        $tieneSubcategorias = Categoria::where('categoria_padre', $id)
                                      ->where('id_estatus', 1)
                                      ->exists();
        
        if ($tieneSubcategorias) {
            return back()->with('error', 'No se puede desactivar una categoría con subcategorías.');
        }
        
        DB::beginTransaction();
        
        try {
            $categoria->update(['id_estatus' => 2]); // 2 = Inactivo
            
            // Registrar movimiento
            Movimiento::registrar(
                    Auth::user()->usuario->id_usuario,
                'desactivar',
                'categorias',
                $categoria->id_categoria,
                null,
                "Desactivación de categoría {$categoria->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('categorias.index')
                            ->with('success', 'Categoría desactivada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al desactivar la categoría: ' . $e->getMessage()]);
        }
    }
}