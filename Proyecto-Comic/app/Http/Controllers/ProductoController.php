<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Comic;
use App\Models\Figura;
use App\Models\Categoria;
use App\Models\Editorial;
use App\Models\Moneda;
use App\Models\Idioma;
use App\Models\Proveedor;
use App\Models\Producto_proveedor;
use App\Models\Imagen;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'editorial', 'moneda']);
        
        // Filtros
        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }
        
        if ($request->filled('editorial')) {
            $query->where('id_editorial', $request->editorial);
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo_producto', $request->tipo);
        }
        
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('codigo_barras', 'like', "%{$request->buscar}%");
            });
        }
        
        $productos = $query->where('id_estatus', 1)
                          ->orderBy('nombre')
                          ->paginate(15);
        
        $categorias = Categoria::where('id_estatus', 1)->orderBy('nombre')->get();
        $editoriales = Editorial::where('id_estatus', 1)->orderBy('nombre')->get();
        
        return view('productos.index', compact('productos', 'categorias', 'editoriales'));
    }
    
    public function create()
    {
        $categorias = Categoria::where('id_estatus', 1)->orderBy('nombre')->get();
        $editoriales = Editorial::where('id_estatus', 1)->orderBy('nombre')->get();
        $monedas = Moneda::all();
        $idiomas = Idioma::all();
        $proveedores = Proveedor::where('id_estatus', 1)->orderBy('nombre_empresa')->get();
        
        return view('productos.create', compact('categorias', 'editoriales', 'monedas', 'idiomas', 'proveedores'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'codigo_barras' => 'required|string|max:20|unique:productos,codigo_barras',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'id_moneda' => 'required|exists:monedas,id_moneda',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_editorial' => 'required|exists:editoriales,id_editorial',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:0',
            'tipo_producto' => 'required|in:comic,figura',
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'es_proveedor_principal' => 'required|boolean',
            'precio_proveedor' => 'required|numeric|min:0',
            'imagenes.*' => 'nullable|image|max:2048',
            'imagen_principal' => 'required|integer|min:0',
            
            // Campos para cómic
            'numero_edicion' => 'required_if:tipo_producto,comic|nullable|string|max:20',
            'isbn' => 'nullable|string|max:20',
            'escritor' => 'nullable|string|max:100',
            'ilustrador' => 'nullable|string|max:100',
            'fecha_publicacion' => 'nullable|date',
            'numero_paginas' => 'nullable|integer|min:1',
            'id_idioma' => 'required_if:tipo_producto,comic|nullable|exists:idiomas,id_idioma',
            
            // Campos para figura
            'material' => 'nullable|string|max:50',
            'altura' => 'nullable|numeric|min:0',
            'peso' => 'nullable|numeric|min:0',
            'escala' => 'nullable|string|max:20',
            'personaje' => 'nullable|string|max:100',
            'serie' => 'nullable|string|max:100',
            'artista' => 'nullable|string|max:100',
            'edicion_limitada' => 'boolean',
            'numero_serie' => 'nullable|string|max:50',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Crear producto base
            $producto = Producto::create([
                'codigo_barras' => $request->codigo_barras,
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'precio_compra' => $request->precio_compra,
                'precio_venta' => $request->precio_venta,
                'id_moneda' => $request->id_moneda,
                'id_categoria' => $request->id_categoria,
                'id_editorial' => $request->id_editorial,
                'stock_actual' => $request->stock_actual,
                'stock_minimo' => $request->stock_minimo,
                'stock_maximo' => $request->stock_maximo,
                'tipo_producto' => $request->tipo_producto,
                'id_estatus' => 1
            ]);
            
            // Crear producto específico según el tipo
            if ($request->tipo_producto == 'comic') {
                Comic::create([
                    'id_producto' => $producto->id_producto,
                    'numero_edicion' => $request->numero_edicion,
                    'isbn' => $request->isbn,
                    'escritor' => $request->escritor,
                    'ilustrador' => $request->ilustrador,
                    'fecha_publicacion' => $request->fecha_publicacion,
                    'numero_paginas' => $request->numero_paginas,
                    'id_idioma' => $request->id_idioma
                ]);
            } else {
                Figura::create([
                    'id_producto' => $producto->id_producto,
                    'material' => $request->material,
                    'altura' => $request->altura,
                    'peso' => $request->peso,
                    'escala' => $request->escala,
                    'personaje' => $request->personaje,
                    'serie' => $request->serie,
                    'artista' => $request->artista,
                    'edicion_limitada' => $request->edicion_limitada ?? false,
                    'numero_serie' => $request->numero_serie
                ]);
            }
            
            // Asociar proveedor
            Producto_proveedor::create([
                'id_producto' => $producto->id_producto,
                'id_proveedor' => $request->id_proveedor,
                'es_proveedor_principal' => $request->es_proveedor_principal,
                'precio_proveedor' => $request->precio_proveedor,
                'tiempo_entrega_dias' => $request->tiempo_entrega_dias
            ]);
            
            // Guardar imágenes
            if ($request->hasFile('imagenes')) {
                $imagenes = $request->file('imagenes');
                $imagenPrincipal = $request->imagen_principal;
                
                foreach ($imagenes as $index => $imagen) {
                    $nombreArchivo = time() . '_' . $index . '.' . $imagen->getClientOriginalExtension();
                    $ruta = $imagen->storeAs('productos', $nombreArchivo, 'public');
                    
                    // Crear registro de imagen
                    Imagen::create([
                        'ruta' => $ruta,
                        'nombre_original' => $imagen->getClientOriginalName(),
                        'extension' => $imagen->getClientOriginalExtension(),
                        'mime_type' => $imagen->getMimeType(),
                        'alt_texto' => $request->nombre,
                        'titulo' => $request->nombre,
                        'orden' => $index,
                        'es_principal' => ($index == $imagenPrincipal),
                        'tamanio' => $imagen->getSize(),
                        'imageable_type' => $request->tipo_producto == 'comic' ? 'App\Models\Comic' : 'App\Models\Figura',
                        'imageable_id' => $request->tipo_producto == 'comic' ? 
                                         $producto->comic->id_comic : $producto->figura->id_figura,
                        'id_estatus' => 1
                    ]);
                }
            }
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::user()->usuario->id_usuario,
                'crear',
                'productos',
                $producto->id_producto,
                null,
                "Creación de producto {$producto->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('productos.index')
                            ->with('success', 'Producto creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $producto = Producto::with([
            'categoria', 
            'editorial', 
            'moneda',
            'comic.idioma',
            'figura',
            'proveedores'
        ])->findOrFail($id);
        
        if ($producto->tipo_producto == 'comic') {
            $imagenes = $producto->comic->imagenes;
            $imagenPrincipal = $producto->comic->imagenPrincipal;
        } else {
            $imagenes = $producto->figura->imagenes;
            $imagenPrincipal = $producto->figura->imagenPrincipal;
        }
        
        return view('productos.show', compact('producto', 'imagenes', 'imagenPrincipal'));
    }
    
    public function edit($id)
    {
        $producto = Producto::with([
            'categoria', 
            'editorial', 
            'moneda',
            'comic.idioma',
            'figura',
            'proveedores'
        ])->findOrFail($id);
        
        $categorias = Categoria::where('id_estatus', 1)->orderBy('nombre')->get();
        $editoriales = Editorial::where('id_estatus', 1)->orderBy('nombre')->get();
        $monedas = Moneda::all();
        $idiomas = Idioma::all();
        $proveedores = Proveedor::where('id_estatus', 1)->orderBy('nombre_empresa')->get();
        
        if ($producto->tipo_producto == 'comic') {
            $imagenes = $producto->comic->imagenes;
        } else {
            $imagenes = $producto->figura->imagenes;
        }
        
        return view('productos.edit', compact(
            'producto', 
            'categorias', 
            'editoriales', 
            'monedas', 
            'idiomas', 
            'proveedores',
            'imagenes'
        ));
    }
    
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        $request->validate([
            'codigo_barras' => 'required|string|max:20|unique:productos,codigo_barras,' . $id . ',id_producto',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'id_moneda' => 'required|exists:monedas,id_moneda',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'id_editorial' => 'required|exists:editoriales,id_editorial',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:0',
            'imagenes.*' => 'nullable|image|max:2048',
            'imagen_principal' => 'nullable|integer',
            
            // Campos para cómic
            'numero_edicion' => 'required_if:tipo_producto,comic|nullable|string|max:20',
            'isbn' => 'nullable|string|max:20',
            'escritor' => 'nullable|string|max:100',
            'ilustrador' => 'nullable|string|max:100',
            'fecha_publicacion' => 'nullable|date',
            'numero_paginas' => 'nullable|integer|min:1',
            'id_idioma' => 'required_if:tipo_producto,comic|nullable|exists:idiomas,id_idioma',
            
            // Campos para figura
            'material' => 'nullable|string|max:50',
            'altura' => 'nullable|numeric|min:0',
            'peso' => 'nullable|numeric|min:0',
            'escala' => 'nullable|string|max:20',
            'personaje' => 'nullable|string|max:100',
            'serie' => 'nullable|string|max:100',
            'artista' => 'nullable|string|max:100',
            'edicion_limitada' => 'boolean',
            'numero_serie' => 'nullable|string|max:50',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Actualizar producto base
            $producto->update([
                'codigo_barras' => $request->codigo_barras,
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'precio_compra' => $request->precio_compra,
                'precio_venta' => $request->precio_venta,
                'id_moneda' => $request->id_moneda,
                'id_categoria' => $request->id_categoria,
                'id_editorial' => $request->id_editorial,
                'stock_actual' => $request->stock_actual,
                'stock_minimo' => $request->stock_minimo,
                'stock_maximo' => $request->stock_maximo
            ]);
            
            // Actualizar producto específico según el tipo
            if ($producto->tipo_producto == 'comic') {
                $comic = $producto->comic;
                $comic->update([
                    'numero_edicion' => $request->numero_edicion,
                    'isbn' => $request->isbn,
                    'escritor' => $request->escritor,
                    'ilustrador' => $request->ilustrador,
                    'fecha_publicacion' => $request->fecha_publicacion,
                    'numero_paginas' => $request->numero_paginas,
                    'id_idioma' => $request->id_idioma
                ]);
                
                $entidadEspecifica = $comic;
            } else {
                $figura = $producto->figura;
                $figura->update([
                    'material' => $request->material,
                    'altura' => $request->altura,
                    'peso' => $request->peso,
                    'escala' => $request->escala,
                    'personaje' => $request->personaje,
                    'serie' => $request->serie,
                    'artista' => $request->artista,
                    'edicion_limitada' => $request->edicion_limitada ?? false,
                    'numero_serie' => $request->numero_serie
                ]);
                
                $entidadEspecifica = $figura;
            }
            
            // Actualizar imágenes
            if ($request->hasFile('imagenes')) {
                $imagenes = $request->file('imagenes');
                
                foreach ($imagenes as $index => $imagen) {
                    $nombreArchivo = time() . '_' . $index . '.' . $imagen->getClientOriginalExtension();
                    $ruta = $imagen->storeAs('productos', $nombreArchivo, 'public');
                    
                    // Crear registro de imagen
                    Imagen::create([
                        'ruta' => $ruta,
                        'nombre_original' => $imagen->getClientOriginalName(),
                        'extension' => $imagen->getClientOriginalExtension(),
                        'mime_type' => $imagen->getMimeType(),
                        'alt_texto' => $request->nombre,
                        'titulo' => $request->nombre,
                        'orden' => $index + 100, // Para que queden después de las existentes
                        'es_principal' => false,
                        'tamanio' => $imagen->getSize(),
                        'imageable_type' => $producto->tipo_producto == 'comic' ? 'App\Models\Comic' : 'App\Models\Figura',
                        'imageable_id' => $entidadEspecifica->getKey(),
                        'id_estatus' => 1
                    ]);
                }
            }
            
            // Actualizar imagen principal si se indica
            if ($request->has('imagen_principal')) {
                $imagenesActuales = $entidadEspecifica->imagenes;
                
                foreach ($imagenesActuales as $img) {
                    $img->es_principal = ($img->id_imagen == $request->imagen_principal);
                    $img->save();
                }
            }
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::user()->usuario->id_usuario,
                'actualizar',
                'productos',
                $producto->id_producto,
                null,
                "Actualización de producto {$producto->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('productos.index')
                            ->with('success', 'Producto actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Cambiar estatus a inactivo
            $producto->update(['id_estatus' => 2]); // 2 = Inactivo
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::user()->usuario->id_usuario,
                'desactivar',
                'productos',
                $producto->id_producto,
                null,
                "Desactivación de producto {$producto->nombre}"
            );
            
            DB::commit();
            
            return redirect()->route('productos.index')
                            ->with('success', 'Producto desactivado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al desactivar el producto: ' . $e->getMessage()]);
        }
    }
    
    public function buscar(Request $request)
    {
        $term = $request->input('term');
        
        $productos = Producto::where(function($query) use ($term) {
                        $query->where('nombre', 'like', "%{$term}%")
                              ->orWhere('codigo_barras', 'like', "%{$term}%");
                    })
                    ->where('id_estatus', 1)
                    ->where('stock_actual', '>', 0)
                    ->with(['moneda'])
                    ->limit(10)
                    ->get();
        
        $results = $productos->map(function($producto) {
            return [
                'id' => $producto->id_producto,
                'text' => $producto->codigo_barras . ' - ' . $producto->nombre,
                'precio' => $producto->precio_venta,
                'moneda' => $producto->moneda->simbolo,
                'stock' => $producto->stock_actual
            ];
        });
        
        return response()->json(['results' => $results]);
    }
}