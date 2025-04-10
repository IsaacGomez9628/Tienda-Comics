@extends('layouts.master')

@section('title', 'Stock de Productos - Tienda de Cómics')

@section('content')
<div class="container">
    <h2 class="text-center mb-4">Stock de Productos</h2>

    <!-- Carrusel (opcional) -->
    <div id="carouselExample" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('img/promo1.jpg') }}" class="d-block w-100" alt="Promo 1" style="height: 300px; object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/promo2.jpg') }}" class="d-block w-100" alt="Promo 2" style="height: 300px; object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/promo3.jpg') }}" class="d-block w-100" alt="Promo 3" style="height: 300px; object-fit: cover;">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <!-- Filtros -->
    <form action="{{ route('productos.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-2">
                <select name="categoria" class="form-select">
                    <option value="">Filtrar por categoría</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id_categoria }}" {{ request('categoria') == $categoria->id_categoria ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select name="editorial" class="form-select">
                    <option value="">Filtrar por editorial</option>
                    @foreach($editoriales as $editorial)
                        <option value="{{ $editorial->id_editorial }}" {{ request('editorial') == $editorial->id_editorial ? 'selected' : '' }}>
                            {{ $editorial->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select name="tipo" class="form-select">
                    <option value="">Filtrar por tipo</option>
                    <option value="comic" {{ request('tipo') == 'comic' ? 'selected' : '' }}>Cómic</option>
                    <option value="figura" {{ request('tipo') == 'figura' ? 'selected' : '' }}>Figura</option>
                </select>
            </div>
            <div class="col-md-8 mb-2">
                <div class="input-group">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o código" value="{{ request('buscar') }}">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
            <div class="col-md-4 mb-2 text-end">
                @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                    <a href="{{ route('productos.create') }}" class="btn btn-success">Nuevo Producto</a>
                @endif
            </div>
        </div>
    </form>

    <!-- Productos -->
    <div class="row">
        @forelse($productos as $producto)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @php
                        $imagen = null;
                        if ($producto->tipo_producto == 'comic' && $producto->comic) {
                            $imagen = $producto->comic->imagenPrincipal();
                        } elseif ($producto->tipo_producto == 'figura' && $producto->figura) {
                            $imagen = $producto->figura->imagenPrincipal();
                        }
                    @endphp
                    
                    @if($imagen)
                        <img src="{{ asset('storage/' . $imagen->ruta) }}" class="card-img-top" alt="{{ $producto->nombre }}" style="height: 300px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                            <span class="text-muted">Sin imagen</span>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $producto->nombre }}</h5>
                        <p class="card-text"><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
                        <p class="card-text"><strong>Proveedor:</strong> {{ $producto->editorial->nombre }}</p>
                        <p class="card-text"><strong>Descripción:</strong> {{ Str::limit($producto->descripcion, 100) }}</p>
                        <p class="card-text"><strong>Precio:</strong> ${{ number_format($producto->precio_venta, 2) }}</p>
                        <p class="card-text @if($producto->stock_actual <= $producto->stock_minimo) text-danger @endif">
                            <strong>Stock:</strong> {{ $producto->stock_actual }} unidades
                        </p>
                        
                        <div class="d-flex justify-content-between mt-auto">
                            @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                                <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash-alt"></i> Borrar
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('productos.show', $producto->id_producto) }}" class="btn btn-primary w-100">
                                    Ver detalles
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No se encontraron productos con los filtros aplicados.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $productos->appends(request()->all())->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endpush