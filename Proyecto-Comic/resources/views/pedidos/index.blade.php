@extends('layouts.master')

@section('title', 'Pedidos - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Pedidos</h2>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('pedidos.create', ['tipo' => 'proveedor']) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Pedido a Proveedor
                </a>
                <a href="{{ route('pedidos.create', ['tipo' => 'cliente']) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Pedido a Cliente
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pedidos.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tipo" class="form-label">Tipo de Pedido</label>
                        <select name="tipo" id="tipo" class="form-select">
                            <option value="">Todos los tipos</option>
                            <option value="proveedor" {{ request('tipo') == 'proveedor' ? 'selected' : '' }}>Pedidos a Proveedores</option>
                            <option value="cliente" {{ request('tipo') == 'cliente' ? 'selected' : '' }}>Pedidos de Clientes</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Pendiente</option>
                            <option value="2" {{ request('estado') == '2' ? 'selected' : '' }}>Enviado</option>
                            <option value="3" {{ request('estado') == '3' ? 'selected' : '' }}>Recibido</option>
                            <option value="4" {{ request('estado') == '4' ? 'selected' : '' }}>Confirmado</option>
                            <option value="5" {{ request('estado') == '5' ? 'selected' : '' }}>Cancelado</option>
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
                        <label for="buscar" class="form-label">Buscar (Folio o Nombre)</label>
                        <input type="text" name="buscar" id="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Buscar por folio o nombre...">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-eraser"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Pedidos -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Folio</th>
                            <th>Tipo</th>
                            <th>Destinatario</th>
                            <th>Fecha Solicitud</th>
                            <th>Fecha Entrega Estimada</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidos as $pedido)
                            <tr>
                                <td>{{ $pedido->folio }}</td>
                                <td>
                                    @if($pedido->proveedor)
                                        <span class="badge bg-info">Proveedor</span>
                                    @elseif($pedido->cliente)
                                        <span class="badge bg-primary">Cliente</span>
                                    @else
                                        <span class="badge bg-secondary">Desconocido</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pedido->proveedor)
                                        {{ $pedido->proveedor->nombre_empresa }}
                                    @elseif($pedido->cliente)
                                        {{ $pedido->cliente->persona->nombreCompleto() }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($pedido->fecha_entrega_estimada)
                                        {{ $pedido->fecha_entrega_estimada->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">No definida</span>
                                    @endif
                                </td>
                                <td>{{ $pedido->moneda->simbolo }}{{ number_format($pedido->total, 2) }}</td>
                                <td>
                                    <span class="badge 
                                        @if($pedido->estadoPedido->nombre == 'Pendiente') bg-warning text-dark
                                        @elseif($pedido->estadoPedido->nombre == 'Enviado' || $pedido->estadoPedido->nombre == 'Confirmado') bg-info
                                        @elseif($pedido->estadoPedido->nombre == 'Recibido') bg-success
                                        @elseif($pedido->estadoPedido->nombre == 'Cancelado') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ $pedido->estadoPedido->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('pedidos.show', $pedido->id_pedido) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                                            <a href="{{ route('pedidos.edit', $pedido->id_pedido) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay pedidos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $pedidos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection