<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movimiento;
use App\Models\Tipo_movimiento;

class MovimientoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    public function index(Request $request)
    {
        $query = Movimiento::with(['usuario.persona', 'tipoMovimiento']);
        
        // Filtros
        if ($request->filled('tipo')) {
            $query->where('id_tipo_movimiento', $request->tipo);
        }
        
        if ($request->filled('tabla')) {
            $query->where('tabla_afectada', $request->tabla);
        }
        
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin . ' 23:59:59']);
        }
        
        if ($request->filled('usuario')) {
            $query->where('id_usuario', $request->usuario);
        }
        
        $movimientos = $query->orderBy('created_at', 'desc')
                          ->paginate(20);
        
        $tiposMovimiento = Tipo_movimiento::all();
        $tablas = Movimiento::select('tabla_afectada')->distinct()->pluck('tabla_afectada');
        $usuarios = \App\Models\Usuario::with('persona')->get();
        
        return view('movimientos.index', compact('movimientos', 'tiposMovimiento', 'tablas', 'usuarios'));
    }
    
    public function show($id)
    {
        $movimiento = Movimiento::with(['usuario.persona', 'tipoMovimiento'])->findOrFail($id);
        return view('movimientos.show', compact('movimiento'));
    }
    
    /**
     * Registrar un nuevo movimiento en el sistema
     *
     * @param int $idUsuario ID del usuario que realiza el movimiento
     * @param string $tipoMovimiento Tipo de movimiento (crear, actualizar, eliminar, etc.)
     * @param string $tablaAfectada Nombre de la tabla afectada
     * @param int $idRegistroAfectado ID del registro afectado
     * @param string|null $valorAnterior Valor anterior (opcional)
     * @param string|null $valorNuevo Valor nuevo o descripción del movimiento
     * @return Movimiento
     */
    public static function registrar($idUsuario, $tipoMovimiento, $tablaAfectada, $idRegistroAfectado, $valorAnterior = null, $valorNuevo = null)
    {
        // Buscar el tipo de movimiento
        $tipoObj = Tipo_movimiento::where('slug', $tipoMovimiento)->first();
        
        if (!$tipoObj) {
            // Crear tipo de movimiento si no existe
            $tipoObj = Tipo_movimiento::create([
                'nombre' => ucfirst(str_replace('_', ' ', $tipoMovimiento)),
                'slug' => $tipoMovimiento,
                'descripcion' => 'Tipo de movimiento generado automáticamente'
            ]);
        }
        
        // Registrar el movimiento
        $movimiento = Movimiento::create([
            'id_usuario' => $idUsuario,
            'id_tipo_movimiento' => $tipoObj->id_tipo_movimiento,
            'tabla_afectada' => $tablaAfectada,
            'id_registro_afectado' => $idRegistroAfectado,
            'valor_anterior' => $valorAnterior,
            'valor_nuevo' => $valorNuevo,
            'ip' => request()->ip(),
            'agente_usuario' => substr(request()->userAgent(), 0, 255)
        ]);
        
        return $movimiento;
    }
}