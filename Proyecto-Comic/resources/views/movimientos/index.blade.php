@extends('layouts.master')

@section('title', 'Historial de Actividades - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Historial de Actividades</h2>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('movimientos.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tipo" class="form-label">Tipo de Movimiento</label>
                        <select name="tipo" id="tipo" class="form-select">
                            <option value="">Todos los tipos</option>
                            @foreach($tiposMovimiento as $tipo)
                                <option value="{{ $tipo->id_tipo_movimiento }}" {{ request('tipo') == $tipo->id_tipo_movimiento ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tabla" class="form-label">Tabla Afectada</label>
                        <select name="tabla" id="tabla" class="form-select">
                            <option value="">Todas las tablas</option>
                            @foreach($tablas as $tabla)
                                <option value="{{ $tabla }}" {{ request('tabla') == $tabla ? 'selected' : '' }}>
                                    {{ ucfirst($tabla) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_inicio" class="form-label">Desde</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="fecha_fin" class="form-label">Hasta</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <select name="usuario" id="usuario" class="form-select">
                            <option value="">Todos los usuarios</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id_usuario }}" {{ request('usuario') == $usuario->id_usuario ? 'selected' : '' }}>
                                    {{ $usuario->persona->nombreCompleto() }} ({{ $usuario->nombre_usuario }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <a href="{{ route('movimientos.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-eraser"></i> Limpiar
                        </a>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" name="export" value="1" class="btn btn-success w-100">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Movimientos -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Tabla</th>
                            <th>Registro</th>
                            <th>Descripción</th>
                            <th>IP</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                            <tr>
                                <td>{{ $movimiento->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @if($movimiento->usuario)
                                        {{ $movimiento->usuario->nombre_usuario }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($movimiento->tipoMovimiento->nombre == 'crear') bg-success
                                        @elseif($movimiento->tipoMovimiento->nombre == 'actualizar') bg-primary
                                        @elseif($movimiento->tipoMovimiento->nombre == 'eliminar' || $movimiento->tipoMovimiento->nombre == 'desactivar') bg-danger
                                        @else bg-info
                                        @endif">
                                        {{ ucfirst($movimiento->tipoMovimiento->nombre) }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($movimiento->tabla_afectada) }}</td>
                                <td>{{ $movimiento->id_registro_afectado }}</td>
                                <td>
                                    @if($movimiento->valor_nuevo)
                                        {{ Str::limit($movimiento->valor_nuevo, 50) }}
                                    @else
                                        <span class="text-muted">Sin descripción</span>
                                    @endif
                                </td>
                                <td>{{ $movimiento->ip }}</td>
                                <td>
                                    <a href="{{ route('movimientos.show', $movimiento->id_movimiento) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay movimientos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $movimientos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection