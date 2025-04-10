@extends('layouts.master')

@section('title', $membresia->nombre . ' - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header {{ $membresia->puntos_requeridos == 0 ? 'bg-primary text-white' : ($membresia->porcentaje_descuento >= 20 ? 'bg-danger text-white' : 'bg-warning text-dark') }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title mb-0">{{ $membresia->nombre }}</h2>
                        <a href="{{ route('membresias.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <span class="display-4">{{ $membresia->porcentaje_descuento }}%</span>
                        <p class="text-muted">de descuento en compras</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2 mb-3">Detalles de la Membresía</h5>
                        <p><strong>Nombre:</strong> {{ $membresia->nombre }}</p>
                        <p><strong>Puntos Requeridos:</strong> {{ number_format($membresia->puntos_requeridos) }}</p>
                        <p><strong>Descuento:</strong> {{ $membresia->porcentaje_descuento }}%</p>
                        
                        @if($membresia->descripcion)
                            <p><strong>Descripción:</strong> {{ $membresia->descripcion }}</p>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('membresias.edit', $membresia->id_membresia) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        
                        @if($membresia->puntos_requeridos > 0) {{-- No permitir eliminar la membresía básica --}}
                            <form action="{{ route('membresias.destroy', $membresia->id_membresia) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar esta membresía?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt"></i> Desactivar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title mb-0">Clientes con esta Membresía</h3>
                </div>
                <div class="card-body">
                    @if(count($clientesActivos) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Puntos</th>
                                        <th>Desde</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clientesActivos as $clienteMembresia)
                                        <tr>
                                            <td>{{ $clienteMembresia->cliente->codigo_cliente }}</td>
                                            <td>{{ $clienteMembresia->cliente->persona->nombreCompleto() }}</td>
                                            <td>{{ number_format($clienteMembresia->cliente->puntos_acumulados) }}</td>
                                            <td>{{ $clienteMembresia->fecha_inicio->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('clientes.show', $clienteMembresia->cliente->id_cliente) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No hay clientes con esta membresía actualmente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection