@extends('layouts.master')

@section('title', 'Membresías - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Membresías</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('membresias.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nueva Membresía
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

    <div class="row">
        @forelse($membresias as $membresia)
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100 {{ $loop->first ? 'border-primary' : ($loop->last ? 'border-danger' : 'border-warning') }}" 
                     style="{{ $loop->first ? 'border-width: 2px' : ($loop->last ? 'border-width: 2px' : 'border-width: 2px') }}">
                    <div class="card-header {{ $loop->first ? 'bg-primary text-white' : ($loop->last ? 'bg-danger text-white' : 'bg-warning text-dark') }}">
                        <h3 class="card-title mb-0 text-center">{{ $membresia->nombre }}</h3>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="text-center mb-3">
                            <span class="display-4">{{ $membresia->porcentaje_descuento }}%</span>
                            <p class="text-muted">de descuento</p>
                        </div>
                        
                        <div class="mb-3">
                            <p><strong>Puntos Requeridos:</strong> {{ number_format($membresia->puntos_requeridos) }}</p>
                            @if($membresia->descripcion)
                                <p>{{ $membresia->descripcion }}</p>
                            @endif
                        </div>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('membresias.show', $membresia->id_membresia) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-users"></i> Ver Clientes
                                </a>
                                <div>
                                    <a href="{{ route('membresias.edit', $membresia->id_membresia) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$loop->first) {{-- No permitir eliminar la membresía básica --}}
                                        <form action="{{ route('membresias.destroy', $membresia->id_membresia) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('¿Estás seguro de desactivar esta membresía?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No hay membresías registradas. ¡Crea la primera!
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection