@extends('layouts.master')

@section('title', 'Perfil de Empleado - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title mb-0">Perfil de Empleado</h2>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="avatar-container mb-3">
                                <span class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto;">
                                    {{ strtoupper(substr($usuario->persona->nombre, 0, 1)) }}{{ strtoupper(substr($usuario->persona->apellido_paterno, 0, 1)) }}
                                </span>
                            </div>
                            <h4>{{ $usuario->nombre_usuario }}</h4>
                            <span class="badge {{ $usuario->rol->nombre == 'Administrador' ? 'bg-danger' : 'bg-info' }}">
                                {{ $usuario->rol->nombre }}
                            </span>
                        </div>
                        <div class="col-md-8">
                            <h3 class="border-bottom pb-2 mb-3">Información Personal</h3>
                            
                            <p><strong>Nombre Completo:</strong> {{ $usuario->persona->nombreCompleto() }}</p>
                            <p><strong>Correo Electrónico:</strong> {{ $usuario->persona->email }}</p>
                            
                            @if($usuario->persona->telefono)
                                <p><strong>Teléfono:</strong> {{ $usuario->persona->telefono }}</p>
                            @endif
                            
                            @if($usuario->persona->fecha_nacimiento)
                                <p><strong>Fecha de Nacimiento:</strong> {{ $usuario->persona->fecha_nacimiento->format('d/m/Y') }}</p>
                            @endif
                            
                            <p><strong>Matrícula:</strong> {{ $usuario->id_usuario }}</p>
                            
                            <p>
                                <strong>Estado:</strong> 
                                <span class="badge {{ $usuario->id_estatus == 1 ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $usuario->id_estatus == 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                            
                            @if($usuario->ultima_sesion)
                                <p><strong>Última Sesión:</strong> {{ $usuario->ultima_sesion->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    @if(Auth::user()->usuario->id_usuario == $usuario->id_usuario || (Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre == 'Administrador'))
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Información
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Movimientos recientes (solo visible para administradores o el propio usuario) -->
            @if(Auth::user()->usuario->id_usuario == $usuario->id_usuario || (Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre == 'Administrador'))
                <div class="card shadow mt-4">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title mb-0">Actividades Recientes</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Acción</th>
                                        <th>Tabla</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($movimientos) && count($movimientos) > 0)
                                        @foreach($movimientos as $movimiento)
                                            <tr>
                                                <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $movimiento->tipoMovimiento->nombre }}</td>
                                                <td>{{ ucfirst($movimiento->tabla_afectada) }}</td>
                                                <td>{{ $movimiento->valor_nuevo }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">No hay actividades recientes registradas</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-container {
        display: flex;
        justify-content: center;
    }
</style>
@endpush