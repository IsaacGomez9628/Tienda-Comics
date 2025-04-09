<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Movimiento extends Model
{
    use HasFactory;
    
    protected $table = 'movimientos';
    protected $primaryKey = 'id_movimiento';
    
    protected $fillable = [
        'id_usuario',
        'id_tipo_movimiento',
        'tabla_afectada',
        'id_registro_afectado',
        'valor_anterior',
        'valor_nuevo',
        'ip',
        'agente_usuario'
    ];
    
    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
    
    // Relación con tipo de movimiento
    public function tipoMovimiento()
    {
        return $this->belongsTo(Tipo_movimiento::class, 'id_tipo_movimiento');
    }
    
    /**
     * Registrar un nuevo movimiento en el sistema
     *
     * @param int|null 
     * @param string 
     * @param string 
     * @param int 
     * @param string|null 
     * @param string|null 
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
        
        // Si se pasa el ID de la tabla users, obtener el ID de usuario correspondiente
        if ($idUsuario === null) {
            // Si no se proporcionó un ID, usar el usuario autenticado
            if (Auth::check()) {
                // Intentar obtener el ID del usuario desde la relación usuario
                if (Auth::user()->usuario) {
                    $idUsuario = Auth::user()->usuario->id_usuario;
                } else {
                    // Falló todo, usar ID 1 como sistema
                    $idUsuario = 1;
                }
            } else {
                // No hay usuario autenticado, usar ID 1 como sistema
                $idUsuario = 1;
            }
        }
        
        // Registrar el movimiento
        $movimiento = self::create([
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