@extends('layouts.master')

@section('title', 'Detalle de Venta - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title mb-0">Venta #{{ $venta->folio }}</h2>
                        <a href="{{ route('ventas.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Información de la Venta</h5>
                            <p><strong>Folio:</strong> {{ $venta->folio }}</p>
                            <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge {{ $venta->estatus == 'completada' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($venta->estatus) }}
                                </span>
                            </p>
                            <p><strong>Método de Pago:</strong> {{ ucfirst($venta->metodo_pago) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Cliente</h5>
                            @if($venta->cliente)
                                <p><strong>Nombre:</strong> {{ $venta->cliente->persona->nombreCompleto() }}</p>
                                <p><strong>Código:</strong> {{ $venta->cliente->codigo_cliente }}</p>
                                
                                @if($venta->cliente->persona->email)
                                    <p><strong>Correo:</strong> {{ $venta->cliente->persona->email }}</p>
                                @endif
                                
                                @if($venta->cliente->persona->telefono)
                                    <p><strong>Teléfono:</strong> {{ $venta->cliente->persona->telefono }}</p>
                                @endif
                            @else
                                <p class="text-muted">Cliente casual (venta sin registro)</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Productos Vendidos</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio Unitario</th>
                                            <th>Cantidad</th>
                                            <th>Descuento</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($venta->detalles as $detalle)
                                            <tr>
                                                <td>
                                                    {{ $detalle->producto->nombre }}
                                                </td>
                                                <td>{{ $venta->moneda->simbolo }}{{ number_format($detalle->precio_unitario, 2) }}</td>
                                                <td>{{ $detalle->cantidad }}</td>
                                                <td>{{ $venta->moneda->simbolo }}{{ number_format($detalle->descuento ?? 0, 2) }}</td>
                                                <td>{{ $venta->moneda->simbolo }}{{ number_format($detalle->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                            <td>{{ $venta->moneda->simbolo }}{{ number_format($venta->subtotal, 2) }}</td>
                                        </tr>
                                        @if($venta->descuento > 0)
                                            <tr>
                                                <td colspan="4" class="text-end"><strong>Descuento:</strong></td>
                                                <td>{{ $venta->moneda->simbolo }}{{ number_format($venta->descuento, 2) }}</td>
                                            </tr>
                                        @endif
                                        @if($venta->impuesto > 0)
                                            <tr>
                                                <td colspan="4" class="text-end"><strong>Impuesto:</strong></td>
                                                <td>{{ $venta->moneda->simbolo }}{{ number_format($venta->impuesto, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>{{ $venta->moneda->simbolo }}{{ number_format($venta->total, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    @if($venta->historialCompra)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <p class="mb-0"><strong>Puntos ganados por esta compra:</strong> {{ $venta->historialCompra->puntos_ganados }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('ventas.imprimir', $venta->id_venta) }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-print"></i> Imprimir Ticket
                        </a>
                        
                        @if($venta->estatus === 'completada')
                            <form action="{{ route('ventas.cancelar', $venta->id_venta) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta venta?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Cancelar Venta
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Datos del Vendedor</h3>
                </div>
                <div class="card-body">
                    <p><strong>Vendedor:</strong> {{ $venta->usuario->persona->nombreCompleto() }}</p>
                    @if($venta->usuario->rol)
                        <p><strong>Puesto:</strong> {{ $venta->usuario->rol->nombre }}</p>
                    @endif
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title mb-0">Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Venta
                        </a>
                        
                        @if($venta->cliente)
                            <a href="{{ route('clientes.show', $venta->cliente->id_cliente) }}" class="btn btn-secondary">
                                <i class="fas fa-user"></i> Ver Cliente
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection