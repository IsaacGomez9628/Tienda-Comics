@extends('layouts.master')

@section('title', 'Dashboard - Tienda de Cómics')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Bienvenido a la Tienda de Cómics y Figuras</h1>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Resumen de ventas -->
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Ventas Hoy</h5>
                    <h2 class="card-text">${{ number_format($ventasHoy, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Ventas del Mes</h5>
                    <h2 class="card-text">${{ number_format($ventasMes, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Clientes Nuevos este Mes</h5>
                    <h2 class="card-text">{{ $clientesNuevosMes }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Productos con bajo stock -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">Productos con Bajo Stock</h5>
                </div>
                <div class="card-body">
                    @if(count($productosBajoStock) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Stock Actual</th>
                                        <th>Stock Mínimo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosBajoStock as $producto)
                                        <tr>
                                            <td>{{ $producto->nombre }}</td>
                                            <td>{{ $producto->categoria->nombre }}</td>
                                            <td>{{ $producto->stock_actual }}</td>
                                            <td>{{ $producto->stock_minimo }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No hay productos con bajo stock</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pedidos pendientes -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Pedidos Pendientes</h5>
                </div>
                <div class="card-body">
                    @if(count($pedidosPendientes) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Proveedor</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedidosPendientes as $pedido)
                                        <tr>
                                            <td>{{ $pedido->folio }}</td>
                                            <td>{{ $pedido->proveedor->nombre_empresa }}</td>
                                            <td>{{ $pedido->estadoPedido->nombre }}</td>
                                            <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No hay pedidos pendientes</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ventas recientes -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Ventas Recientes</h5>
                </div>
                <div class="card-body">
                    @if(count($ventasRecientes) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventasRecientes as $venta)
                                        <tr>
                                            <td>{{ $venta->folio }}</td>
                                            <td>
                                                @if($venta->cliente)
                                                    {{ $venta->cliente->persona->nombreCompleto() }}
                                                @else
                                                    Cliente casual
                                                @endif
                                            </td>
                                            <td>${{ number_format($venta->total, 2) }}</td>
                                            <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No hay ventas recientes</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Productos más vendidos -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    @if(count($productosPopulares) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad Vendida</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosPopulares as $producto)
                                        <tr>
                                            <td>{{ $producto->nombre }}</td>
                                            <td>{{ $producto->total_vendido }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No hay datos de ventas</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Aquí puedes agregar scripts adicionales para el dashboard
</script>
@endpush