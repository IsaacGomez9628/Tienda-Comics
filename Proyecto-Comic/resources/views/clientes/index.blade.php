@extends('layouts.master')

@section('title', 'Clientes Registrados - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Clientes Registrados</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('clientes.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </a>
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

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Puntos</th>
                            <th>Membresía</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->codigo_cliente }}</td>
                                <td>{{ $cliente->persona->nombreCompleto() }}</td>
                                <td>{{ $cliente->persona->email ?? 'N/A' }}</td>
                                <td>{{ $cliente->persona->telefono ?? 'N/A' }}</td>
                                <td>{{ $cliente->puntos_acumulados }}</td>
                                <td>
                                    @php
                                        $membresiaActual = $cliente->membresiaActual();
                                    @endphp
                                    
                                    @if($membresiaActual)
                                        <span class="badge bg-primary">
                                            {{ $membresiaActual->membresia->nombre }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sin membresía</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('clientes.show', $cliente->id_cliente) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clientes.edit', $cliente->id_cliente) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('clientes.destroy', $cliente->id_cliente) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('¿Estás seguro de desactivar este cliente?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay clientes registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection