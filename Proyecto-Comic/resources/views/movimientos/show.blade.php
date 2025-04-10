@extends('layouts.master')

@section('title', 'Detalle de Actividad - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title mb-0">Detalle de Actividad #{{ $movimiento->id_movimiento }}</h2>
                        <a href="{{ route('movimientos.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="border-bottom pb-2 mb-3">Información Básica</h4>
                            <p>
                                <strong>Tipo de Actividad:</strong>
                                <span class="badge 
                                    @if($movimiento->tipoMovimiento->nombre == 'crear') bg-success
                                    @elseif($movimiento->tipoMovimiento->nombre == 'actualizar') bg-primary
                                    @elseif($movimiento->tipoMovimiento->nombre == 'eliminar' || $movimiento->tipoMovimiento->nombre == 'desactivar') bg-danger
                                    @else bg-info
                                    @endif">
                                    {{ ucfirst($movimiento->tipoMovimiento->nombre) }}
                                </span>
                            </p>
                            <p><strong>Tabla Afectada:</strong> {{ ucfirst($movimiento->tabla_afectada) }}</p>
                            <p><strong>ID del Registro:</strong> {{ $movimiento->id_registro_afectado }}</p>
                            <p><strong>Fecha y Hora:</strong> {{ $movimiento->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4 class="border-bottom pb-2 mb-3">Usuario</h4>
                            @if($movimiento->usuario)
                                <p><strong>Usuario:</strong> {{ $movimiento->usuario->nombre_usuario }}</p>
                                <p><strong>Nombre Completo:</strong> {{ $movimiento->usuario->persona->nombreCompleto() }}</p>
                                <p><strong>Rol:</strong> {{ $movimiento->usuario->rol->nombre }}</p>
                                <p><strong>Dirección IP:</strong> {{ $movimiento->ip }}</p>
                            @else
                                <p class="text-muted">No hay información del usuario disponible.</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="border-bottom pb-2 mb-3">Detalles del Cambio</h4>
                            
                            @if($movimiento->valor_anterior && $movimiento->valor_nuevo)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Valor Anterior</h5>
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0" style="white-space: pre-wrap;">{{ $movimiento->valor_anterior }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Valor Nuevo</h5>
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0" style="white-space: pre-wrap;">{{ $movimiento->valor_nuevo }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($movimiento->valor_nuevo)
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Descripción</h5>
                                    </div>
                                    <div class="card-body">
                                        <pre class="mb-0" style="white-space: pre-wrap;">{{ $movimiento->valor_nuevo }}</pre>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No hay información detallada sobre este cambio.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h4 class="border-bottom pb-2 mb-3">Información Técnica</h4>
                            <p><strong>Agente de Usuario:</strong> {{ $movimiento->agente_usuario }}</p>
                            <p><strong>ID de Movimiento:</strong> {{ $movimiento->id_movimiento }}</p>
                            @if($movimiento->created_at != $movimiento->updated_at)
                                <p><strong>Última Actualización:</strong> {{ $movimiento->updated_at->format('d/m/Y H:i:s') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection