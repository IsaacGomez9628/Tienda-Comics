@extends('layouts.master')

@section('title', 'Detalle de Pedido - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title mb-0">Pedido #{{ $pedido->folio }}</h2>
                        <a href="{{ route('pedidos.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Información del Pedido</h5>
                            <p><strong>Folio:</strong> {{ $pedido->folio }}</p>
                            <p><strong>Fecha de Solicitud:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                            <p>
                                <strong>Fecha Estimada de Entrega:</strong> 
                                @if($pedido->fecha_entrega_estimada)
                                    {{ $pedido->fecha_entrega_estimada->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">No definida</span>
                                @endif
                            </p>
                            <p>
                                <strong>Fecha Real de Entrega:</strong> 
                                @if($pedido->fecha_entrega_real)
                                    {{ $pedido->fecha_entrega_real->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">Pendiente</span>
                                @endif
                            </p>
                            <p>
                                <strong>Estado:</strong> 
                                <span class="badge 
                                    @if($pedido->estadoPedido->nombre == 'Pendiente') bg-warning text-dark
                                    @elseif($pedido->estadoPedido->nombre == 'Enviado' || $pedido->estadoPedido->nombre == 'Confirmado') bg-info
                                    @elseif($pedido->estadoPedido->nombre == 'Recibido') bg-success
                                    @elseif($pedido->estadoPedido->nombre == 'Cancelado') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ $pedido->estadoPedido->nombre }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">
                                @if($pedido->proveedor)
                                    Información del Proveedor
                                @elseif($pedido->cliente)
                                    Información del Cliente
                                @else
                                    Destinatario
                                @endif
                            </h5>
                            
                            @if($pedido->proveedor)
                                <p><strong>Empresa:</strong> {{ $pedido->proveedor->nombre_empresa }}</p>
                                
                                @if($pedido->proveedor->personaContacto)
                                    <p><strong>Contacto:</strong> {{ $pedido->proveedor->personaContacto->nombreCompleto() }}</p>
                                @endif
                                
                                <p><strong>Teléfono:</strong> {{ $pedido->proveedor->telefono }}</p>
                                <p><strong>Correo:</strong> {{ $pedido->proveedor->email }}</p>
                            @elseif($pedido->cliente)
                                <p><strong>Nombre:</strong> {{ $pedido->cliente->persona->nombreCompleto() }}</p>
                                <p><strong>Código:</strong> {{ $pedido->cliente->codigo_cliente }}</p>
                                
                                @if($pedido->cliente->persona->telefono)
                                    <p><strong>Teléfono:</strong> {{ $pedido->cliente->persona->telefono }}</p>
                                @endif
                                
                                @if($pedido->cliente->persona->email)
                                    <p><strong>Correo:</strong> {{ $pedido->cliente->persona->email }}</p>
                                @endif
                            @else
                                <p class="text-muted">Sin información del destinatario</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Productos del Pedido</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio Unitario</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pedido->detalles as $detalle)
                                            <tr>
                                                <td>
                                                    {{ $detalle->producto->nombre }}
                                                </td>
                                                <td>{{ $pedido->moneda->simbolo }}{{ number_format($detalle->precio_unitario, 2) }}</td>
                                                <td>{{ $detalle->cantidad }}</td>
                                                <td>{{ $pedido->moneda->simbolo }}{{ number_format($detalle->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td>{{ $pedido->moneda->simbolo }}{{ number_format($pedido->subtotal, 2) }}</td>
                                        </tr>
                                        @if($pedido->impuesto > 0)
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Impuesto:</strong></td>
                                                <td>{{ $pedido->moneda->simbolo }}{{ number_format($pedido->impuesto, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>{{ $pedido->moneda->simbolo }}{{ number_format($pedido->total, 2) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    @if($pedido->notas)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Notas</h5>
                                <p>{{ $pedido->notas }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between">
                        @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                            <a href="{{ route('pedidos.edit', $pedido->id_pedido) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @else
                            <div></div>
                        @endif
                        
                        <div class="btn-group">
                            @if($pedido->estadoPedido->nombre == 'Pendiente' || $pedido->estadoPedido->nombre == 'Enviado' || $pedido->estadoPedido->nombre == 'Confirmado')
                                @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                                    <form action="{{ route('pedidos.recibir', $pedido->id_pedido) }}" method="POST" class="me-2">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success" onclick="return confirm('¿Confirmar que este pedido ha sido recibido?')">
                                            <i class="fas fa-check-circle"></i> Marcar como Recibido
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('pedidos.cancelar', $pedido->id_pedido) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de cancelar este pedido?')">
                                        <i class="fas fa-times-circle"></i> Cancelar Pedido
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($pedido->proveedor && count($pedido->comunicaciones) > 0)
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title mb-0">Comunicaciones con el Proveedor</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($pedido->comunicaciones as $comunicacion)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $comunicacion->asunto }}</h5>
                                        <small>{{ $comunicacion->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <p class="mb-1">{{ $comunicacion->contenido }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>Enviado a: {{ $comunicacion->email_destino }}</small>
                                        <span class="badge {{ $comunicacion->estatus == 'enviado' ? 'bg-success' : ($comunicacion->estatus == 'confirmado' ? 'bg-info' : 'bg-secondary') }}">
                                            {{ ucfirst($comunicacion->estatus) }}
                                        </span>
                                    </div>
                                    
                                    @if($comunicacion->respuesta)
                                        <div class="alert alert-light mt-2">
                                            <div class="d-flex justify-content-between">
                                                <strong>Respuesta:</strong>
                                                <small>{{ $comunicacion->fecha_respuesta ? $comunicacion->fecha_respuesta->format('d/m/Y H:i') : '' }}</small>
                                            </div>
                                            <p class="mb-0">{{ $comunicacion->respuesta }}</p>
                                        </div>
                                    @elseif(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                                        <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="collapse" data-bs-target="#respuesta_{{ $comunicacion->id_comunicacion }}">
                                            Registrar Respuesta
                                        </button>
                                        <div class="collapse mt-2" id="respuesta_{{ $comunicacion->id_comunicacion }}">
                                            <form action="{{ route('pedidos.registrarRespuesta', $pedido->id_pedido) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id_comunicacion" value="{{ $comunicacion->id_comunicacion }}">
                                                
                                                <div class="mb-3">
                                                    <label for="respuesta_{{ $comunicacion->id_comunicacion }}" class="form-label">Respuesta del Proveedor</label>
                                                    <textarea class="form-control" name="respuesta" id="respuesta_{{ $comunicacion->id_comunicacion }}" rows="3" required></textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Estado de la Respuesta</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="estatus" id="estatus_recibido_{{ $comunicacion->id_comunicacion }}" value="recibido" checked>
                                                        <label class="form-check-label" for="estatus_recibido_{{ $comunicacion->id_comunicacion }}">
                                                            Recibido
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="estatus" id="estatus_confirmado_{{ $comunicacion->id_comunicacion }}" value="confirmado">
                                                        <label class="form-check-label" for="estatus_confirmado_{{ $comunicacion->id_comunicacion }}">
                                                            Confirmado
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="estatus" id="estatus_rechazado_{{ $comunicacion->id_comunicacion }}" value="rechazado">
                                                        <label class="form-check-label" for="estatus_rechazado_{{ $comunicacion->id_comunicacion }}">
                                                            Rechazado
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary">Guardar Respuesta</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-3">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">Datos del Empleado</h3>
                </div>
                <div class="card-body">
                    <p><strong>Responsable:</strong> {{ $pedido->usuario->persona->nombreCompleto() }}</p>
                    @if($pedido->usuario->rol)
                        <p><strong>Puesto:</strong> {{ $pedido->usuario->rol->nombre }}</p>
                    @endif
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title mb-0">Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('pedidos.create', ['tipo' => $pedido->proveedor ? 'proveedor' : 'cliente']) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Pedido
                        </a>
                        
                        @if($pedido->proveedor && $pedido->estadoPedido->nombre == 'Pendiente' && Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                            <form action="{{ route('pedidos.enviarCorreo', $pedido->id_pedido) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-envelope"></i> Enviar por Correo
                                </button>
                            </form>
                        @endif
                        
                        @if($pedido->proveedor)
                            <a href="{{ route('proveedores.show', $pedido->proveedor->id_proveedor) }}" class="btn btn-secondary">
                                <i class="fas fa-building"></i> Ver Proveedor
                            </a>
                        @elseif($pedido->cliente)
                            <a href="{{ route('clientes.show', $pedido->cliente->id_cliente) }}" class="btn btn-secondary">
                                <i class="fas fa-user"></i> Ver Cliente
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Historial de Estado -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title mb-0">Historial de Estados</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Creado</strong>
                                <div><small>{{ $pedido->created_at->format('d/m/Y H:i') }}</small></div>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                <i class="fas fa-check"></i>
                            </span>
                        </li>
                        
                        @foreach($pedido->movimientos ?? [] as $movimiento)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $movimiento->tipoMovimiento->nombre }}</strong>
                                    <div><small>{{ $movimiento->created_at->format('d/m/Y H:i') }}</small></div>
                                </div>
                                <span class="badge bg-info rounded-pill">
                                    <i class="fas fa-check"></i>
                                </span>
                            </li>
                        @endforeach
                        
                        @if($pedido->fecha_entrega_real)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Recibido</strong>
                                    <div><small>{{ $pedido->fecha_entrega_real->format('d/m/Y H:i') }}</small></div>
                                </div>
                                <span class="badge bg-success rounded-pill">
                                    <i class="fas fa-check"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection