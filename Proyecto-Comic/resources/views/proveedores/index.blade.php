@extends('layouts.master')

@section('title', 'Proveedores Registrados - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Proveedores Registrados</h2>
        </div>
        <div class="col-md-4 text-end">
            @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                <a href="{{ route('proveedores.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </a>
            @endif
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
                            <th>Nombre de Empresa</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $proveedor)
                            <tr>
                                <td>{{ $proveedor->nombre_empresa }}</td>
                                <td>
                                    @if($proveedor->personaContacto)
                                        {{ $proveedor->personaContacto->nombreCompleto() }}
                                    @else
                                        <span class="text-muted">No registrado</span>
                                    @endif
                                </td>
                                <td>{{ $proveedor->telefono }}</td>
                                <td>{{ $proveedor->email }}</td>
                                <td class="text-center">
                                    @if($proveedor->productos_count)
                                        <span class="badge bg-info">{{ $proveedor->productos_count }}</span>
                                    @else
                                        <span class="badge bg-secondary">0</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('proveedores.show', $proveedor->id_proveedor) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                                            <a href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('proveedores.destroy', $proveedor->id_proveedor) }}" method="POST" class="d-inline" 
                                                onsubmit="return confirm('¿Estás seguro de desactivar este proveedor?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay proveedores registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection