@extends('layouts.master')

@section('title', 'Detalle de Cliente - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Perfil del Cliente</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-container mb-3">
                            <span class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto;">
                                {{ strtoupper(substr($cliente->persona->nombre, 0, 1)) }}{{ strtoupper(substr($cliente->persona->apellido_paterno, 0, 1)) }}
                            </span>
                        </div>
                        <h4>{{ $cliente->persona->nombreCompleto() }}</h4>
                        <p class="text-muted">{{ $cliente->codigo_cliente }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="border-bottom pb-2 mb-3">Información de Contacto</h5>
                        
                        @if($cliente->persona->email)
                            <p><strong>Correo Electrónico:</strong> {{ $cliente->persona->email }}</p>
                        @endif
                        
                        @if($cliente->persona->telefono)
                            <p><strong>Teléfono:</strong> {{ $cliente->persona->telefono }}</p>
                        @endif
                        
                        @if($cliente->persona->fecha_nacimiento)
                            <p><strong>Fecha de Nacimiento:</strong> {{ $cliente->persona->fecha_nacimiento->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="border-bottom pb-2 mb-3">Información de Membresía</h5>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Puntos acumulados:</span>
                            <span class="badge bg-info">{{ $cliente->puntos_acumulados }}</span>
                        </div>
                        
                        @php
                            $membresiaActual = $cliente->membresiaActual();
                        @endphp
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Membresía actual:</span>
                            @if($membresiaActual)
                                <span class="badge bg-primary">{{ $membresiaActual->membresia->nombre }}</span>
                            @else
                                <span class="badge bg-secondary">Sin membresía</span>
                            @endif
                        </div>
                        
                        @if($membresiaActual)
                            <div class="text-center mt-3">
                                <p class="mb-1"><strong>Descuento:</strong> {{ $membresiaActual->membresia->porcentaje_descuento }}%</p>
                                <p class="mb-1"><strong>Desde:</strong> {{ $membresiaActual->fecha_inicio->format('d/m/Y') }}</p>
                            </div>
                            
                            @if($membresiaActual->membresia->descripcion)
                                <div class="alert alert-info mt-3">
                                    {{ $membresiaActual->membresia->descripcion }}
                                </div>
                            @endif
                        @endif
                    </div>
                    
                    <div class="d-flex flex-column">
                        <a href="{{ route('clientes.edit', $cliente->id_cliente) }}" class="btn btn-warning mb-2">
                            <i class="fas fa-edit"></i> Editar Información
                        </a>
                        <form action="{{ route('clientes.destroy', $cliente->id_cliente) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar este cliente?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash-alt"></i> Desactivar Cliente
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('ventas.create', ['cliente_id' => $cliente->id_cliente]) }}" class="btn btn-success">
                            <i class="fas fa-cash-register"></i> Nueva Venta
                        </a>
                        <a href="{{ route('pedidos.create', ['tipo' => 'cliente', 'cliente_id' => $cliente->id_cliente]) }}" class="btn btn-info">
                            <i class="fas fa-truck"></i> Nuevo Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title mb-0">Historial de Compras</h3>
                </div>
                <div class="card-body">
                    @if(count($historialCompras) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Folio</th>
                                        <th>Total</th>
                                        <th>Puntos Ganados</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historialCompras as $historial)
                                        <tr>
                                            <td>{{ $historial->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $historial->venta->folio }}</td>
                                            <td>${{ number_format($historial->venta->total, 2) }}</td>
                                            <td>{{ $historial->puntos_ganados }}</td>
                                            <td>
                                                <a href="{{ route('ventas.show', $historial->venta->id_venta) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">Este cliente aún no ha realizado compras.</p>
                        <div class="text-center">
                            <a href="{{ route('ventas.create', ['cliente_id' => $cliente->id_cliente]) }}" class="btn btn-success">
                                <i class="fas fa-cash-register"></i> Registrar Primera Venta
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Tabla de Pedidos -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h3 class="card-title mb-0">Pedidos del Cliente</h3>
                </div>
                <div class="card-body">
                    @if(isset($cliente->pedidos) && count($cliente->pedidos) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Folio</th>
                                        <th>Fecha Entrega</th>
                                        <th>Estado</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cliente->pedidos as $pedido)
                                        <tr>
                                            <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $pedido->folio }}</td>
                                            <td>{{ $pedido->fecha_entrega_estimada ? $pedido->fecha_entrega_estimada->format('d/m/Y') : 'N/A' }}</td>
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
                                            <td>${{ number_format($pedido->total, 2) }}</td>
                                            <td>
                                                <a href="{{ route('pedidos.show', $pedido->id_pedido) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">Este cliente aún no tiene pedidos registrados.</p>
                        <div class="text-center">
                            <a href="{{ route('pedidos.create', ['tipo' => 'cliente', 'cliente_id' => $cliente->id_cliente]) }}" class="btn btn-warning">
                                <i class="fas fa-truck"></i> Registrar Nuevo Pedido
                            </a>
                        </div>
                    @endif
                </div>
            </div>
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