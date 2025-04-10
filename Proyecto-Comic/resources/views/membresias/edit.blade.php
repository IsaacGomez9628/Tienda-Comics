@extends('layouts.master')

@section('title', 'Editar Membresía - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h2 class="card-title mb-0">Editar Membresía</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('membresias.update', $membresia->id_membresia) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Nombre -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la Membresía</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $membresia->nombre) }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Porcentaje de Descuento -->
                                <div class="mb-3">
                                    <label for="porcentaje_descuento" class="form-label">Porcentaje de Descuento</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" class="form-control @error('porcentaje_descuento') is-invalid @enderror" id="porcentaje_descuento" name="porcentaje_descuento" value="{{ old('porcentaje_descuento', $membresia->porcentaje_descuento) }}" required>
                                        <span class="input-group-text">%</span>
                                        @error('porcentaje_descuento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <!-- Puntos Requeridos -->
                                <div class="mb-3">
                                    <label for="puntos_requeridos" class="form-label">Puntos Requeridos</label>
                                    <input type="number" min="0" class="form-control @error('puntos_requeridos') is-invalid @enderror" id="puntos_requeridos" name="puntos_requeridos" value="{{ old('puntos_requeridos', $membresia->puntos_requeridos) }}" required>
                                    <div class="form-text">Número de puntos que el cliente debe acumular para obtener esta membresía.</div>
                                    @error('puntos_requeridos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <!-- Descripción -->
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $membresia->descripcion) }}</textarea>
                                    <div class="form-text">Descripción de los beneficios o características de la membresía.</div>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('membresias.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar Membresía</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection