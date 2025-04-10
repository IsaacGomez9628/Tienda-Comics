@extends('layouts.master')

@section('title', 'Empleados Registrados - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Empleados Registrados</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('usuarios.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Empleado
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
                            <th>Matrícula</th>
                            <th>Nombre Completo</th>
                            <th>Nombre de Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id_usuario }}</td>
                                <td>{{ $usuario->persona->nombreCompleto() }}</td>
                                <td>{{ $usuario->nombre_usuario }}</td>
                                <td>{{ $usuario->persona->email }}</td>
                                <td>
                                    <span class="badge {{ $usuario->rol->nombre == 'Administrador' ? 'bg-danger' : 'bg-info' }}">
                                        {{ $usuario->rol->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $usuario->id_estatus == 1 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $usuario->id_estatus == 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('usuarios.show', $usuario->id_usuario) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(Auth::user()->usuario->id_usuario != $usuario->id_usuario)
                                            <form action="{{ route('usuarios.destroy', $usuario->id_usuario) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('¿Estás seguro de desactivar este empleado?')">
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
                                <td colspan="7" class="text-center">No hay empleados registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection