@extends('layouts.master')

@section('title', 'Detalle de Proveedor - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title mb-0">{{ $proveedor->nombre_empresa }}</h2>
                        <a href="{{ route('proveedores.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Información de la Empresa</h5>
                            <p><strong>Nombre:</strong> {{ $proveedor->nombre_empresa }}</p>
                            <p><strong>Teléfono:</strong> {{ $proveedor->telefono }}</p>
                            <p><strong>Correo:</strong> {{ $proveedor->email }}</p>
                            
                            @if($proveedor->pagina_web)
                                <p>
                                    <strong>Página Web:</strong> 
                                    <a href="{{ $proveedor->pagina_web }}" target="_blank">{{ $proveedor->pagina_web }}</a>
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Contacto</h5>
                            
                            @if($proveedor->personaContacto)
                                <p><strong>Nombre:</strong> {{ $proveedor->personaContacto->nombreCompleto() }}</p>
                                <p><strong>Teléfono:</strong> {{ $proveedor->personaContacto->telefono }}</p>
                                <p><strong>Correo:</strong> {{ $proveedor->personaContacto->email }}</p>
                            @else
                                <p class="text-muted">No hay información de contacto registrada</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($proveedor->direccion)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Dirección</h5>
                                
                                <p>
                                    {{ $proveedor->direccion->calle }}
                                    {{ $proveedor->direccion->numero_exterior }}
                                    @if($proveedor->direccion->numero_interior)
                                        Int. {{ $proveedor->direccion->numero_interior }},
                                    @endif
                                    Col. {{ $proveedor->direccion->colonia }},
                                    @if($proveedor->direccion->codigoPostal)
                                        C.P. {{ $proveedor->direccion->codigoPostal->codigo }},
                                    @endif
                                    @if($proveedor->direccion->codigoPostal && $proveedor->direccion->codigoPostal->ciudad)
                                        {{ $proveedor->direccion->codigoPostal->ciudad->nombre }},
                                        {{ $proveedor->direccion->codigoPostal->ciudad->estado->nombre }}
                                    @endif
                                </p>
                                
                                @if($proveedor->direccion->referencias)
                                    <p><strong>Referencias:</strong> {{ $proveedor->direccion->referencias }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($proveedor->notas)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Notas</h5>
                                <p>{{ $proveedor->notas }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('proveedores.destroy', $proveedor->id_proveedor) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar este proveedor?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt"></i> Desactivar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title mb-0">Productos Suministrados</h3>
                </div>
                <div class="card-body">
                    @if(count($productos) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio Compra</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productos as $producto)
                                        <tr>
                                            <td>{{ $producto->codigo_barras }}</td>
                                            <td>{{ $producto->nombre }}</td>
                                            <td>{{ $producto->categoria->nombre }}</td>
                                            <td>${{ number_format($producto->precio_compra, 2) }}</td>
                                            <td class="{{ $producto->stock_actual <= $producto->stock_minimo ? 'text-danger' : '' }}">
                                                {{ $producto->stock_actual }}
                                            </td>
                                            <td>
                                                <a href="{{ route('productos.show', $producto->id_producto) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $productos->links() }}
                        </div>
                    @else
                        <p class="text-center">Este proveedor aún no tiene productos registrados.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('pedidos.create', ['tipo' => 'proveedor', 'proveedor_id' => $proveedor->id_proveedor]) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo Pedido
                        </a>
                        
                        @if($proveedor->email)
                            <a href="mailto:{{ $proveedor->email }}" class="btn btn-info">
                                <i class="fas fa-envelope"></i> Enviar Correo
                            </a>
                        @endif
                        
                        @if($proveedor->telefono)
                            <a href="tel:{{ $proveedor->telefono }}" class="btn btn-secondary">
                                <i class="fas fa-phone"></i> Llamar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h3 class="card-title mb-0">Últimos Pedidos</h3>
                </div>
                <div class="card-body">
                    @if(count($pedidos) > 0)
                        <div class="list-group">
                            @foreach($pedidos as $pedido)
                                <a href="{{ route('pedidos.show', $pedido->id_pedido) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">Folio: {{ $pedido->folio }}</div>
                                        <small>{{ $pedido->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <span class="badge 
                                        @if($pedido->estadoPedido->nombre == 'Pendiente') bg-warning text-dark
                                        @elseif($pedido->estadoPedido->nombre == 'Enviado' || $pedido->estadoPedido->nombre == 'Confirmado') bg-info
                                        @elseif($pedido->estadoPedido->nombre == 'Recibido') bg-success
                                        @elseif($pedido->estadoPedido->nombre == 'Cancelado') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ $pedido->estadoPedido->nombre }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('pedidos.index', ['proveedor_id' => $proveedor->id_proveedor]) }}" class="btn btn-sm btn-outline-secondary w-100">
                                Ver todos los pedidos
                            </a>
                        </div>
                    @else
                        <p class="text-center">No hay pedidos registrados con este proveedor.</p>
                        <div class="text-center">
                            <a href="{{ route('pedidos.create', ['tipo' => 'proveedor', 'proveedor_id' => $proveedor->id_proveedor]) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-plus"></i> Crear Pedido
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection