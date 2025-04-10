@extends('layouts.master')

@section('title', $producto->nombre . ' - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ $producto->nombre }}</h2>
                <a href="{{ route('productos.index') }}" class="btn btn-light">Volver al listado</a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <!-- Imagen Principal -->
                <div class="col-md-5 mb-4">
                    <div id="productImageCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @if($imagenPrincipal)
                                <div class="carousel-item active">
                                    <img src="{{ asset('storage/' . $imagenPrincipal->ruta) }}" class="d-block w-100 rounded" alt="{{ $producto->nombre }}" style="height: 400px; object-fit: contain;">
                                </div>
                            @endif
                            
                            @foreach($imagenes as $imagen)
                                @if(!$imagen->es_principal)
                                    <div class="carousel-item">
                                        <img src="{{ asset('storage/' . $imagen->ruta) }}" class="d-block w-100 rounded" alt="{{ $producto->nombre }}" style="height: 400px; object-fit: contain;">
                                    </div>
                                @endif
                            @endforeach
                            
                            @if(!$imagenPrincipal && count($imagenes) == 0)
                                <div class="carousel-item active">
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                                        <span class="text-muted h4">Sin imagen</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        @if(count($imagenes) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        @endif
                    </div>
                    
                    <!-- Miniaturas -->
                    @if(count($imagenes) > 1)
                        <div class="row mt-2">
                            @foreach($imagenes as $index => $imagen)
                                <div class="col-3 mb-2">
                                    <img src="{{ asset('storage/' . $imagen->ruta) }}" class="img-thumbnail cursor-pointer" 
                                         alt="Miniatura" style="height: 60px; object-fit: cover;"
                                         onclick="document.querySelector('#productImageCarousel .carousel-item:nth-child({{ $index + 1 }})').classList.add('active');
                                                 document.querySelectorAll('#productImageCarousel .carousel-item').forEach((item, i) => { 
                                                     if(i != {{ $index }}) item.classList.remove('active') 
                                                 });">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Información del Producto -->
                <div class="col-md-7">
                    <div class="mb-4">
                        <h3 class="border-bottom pb-2">Información General</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Código de Barras:</strong> {{ $producto->codigo_barras }}</p>
                                <p><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
                                <p><strong>Editorial:</strong> {{ $producto->editorial->nombre }}</p>
                                <p><strong>Tipo:</strong> {{ ucfirst($producto->tipo_producto) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-success h3">${{ number_format($producto->precio_venta, 2) }} {{ $producto->moneda->simbolo }}</p>
                                <p class="@if($producto->stock_actual <= $producto->stock_minimo) text-danger @endif">
                                    <strong>Stock:</strong> {{ $producto->stock_actual }} unidades
                                </p>
                                <p><strong>Stock Mínimo:</strong> {{ $producto->stock_minimo }}</p>
                                <p><strong>Stock Máximo:</strong> {{ $producto->stock_maximo }}</p>
                            </div>
                        </div>
                        
                        @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                            <p><strong>Precio de Compra:</strong> ${{ number_format($producto->precio_compra, 2) }} {{ $producto->moneda->simbolo }}</p>
                        @endif
                        
                        <div class="mt-3">
                            <p><strong>Descripción:</strong></p>
                            <p>{{ $producto->descripcion }}</p>
                        </div>
                        
                        @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                            <div class="d-flex mt-3">
                                <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash-alt"></i> Desactivar
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    @if($producto->tipo_producto == 'comic' && $producto->comic)
                        <div class="mb-4">
                            <h3 class="border-bottom pb-2">Detalles del Cómic</h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    @if($producto->comic->numero_edicion)
                                        <p><strong>Número de Edición:</strong> {{ $producto->comic->numero_edicion }}</p>
                                    @endif
                                    
                                    @if($producto->comic->isbn)
                                        <p><strong>ISBN:</strong> {{ $producto->comic->isbn }}</p>
                                    @endif
                                    
                                    @if($producto->comic->escritor)
                                        <p><strong>Escritor:</strong> {{ $producto->comic->escritor }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($producto->comic->ilustrador)
                                        <p><strong>Ilustrador:</strong> {{ $producto->comic->ilustrador }}</p>
                                    @endif
                                    
                                    @if($producto->comic->fecha_publicacion)
                                        <p><strong>Fecha de Publicación:</strong> {{ $producto->comic->fecha_publicacion->format('d/m/Y') }}</p>
                                    @endif
                                    
                                    @if($producto->comic->numero_paginas)
                                        <p><strong>Número de Páginas:</strong> {{ $producto->comic->numero_paginas }}</p>
                                    @endif
                                    
                                    @if($producto->comic->idioma)
                                        <p><strong>Idioma:</strong> {{ $producto->comic->idioma->nombre }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($producto->tipo_producto == 'figura' && $producto->figura)
                        <div class="mb-4">
                            <h3 class="border-bottom pb-2">Detalles de la Figura</h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    @if($producto->figura->material)
                                        <p><strong>Material:</strong> {{ $producto->figura->material }}</p>
                                    @endif
                                    
                                    @if($producto->figura->altura)
                                        <p><strong>Altura:</strong> {{ $producto->figura->altura }} cm</p>
                                    @endif
                                    
                                    @if($producto->figura->peso)
                                        <p><strong>Peso:</strong> {{ $producto->figura->peso }} gr</p>
                                    @endif
                                    
                                    @if($producto->figura->escala)
                                        <p><strong>Escala:</strong> {{ $producto->figura->escala }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    @if($producto->figura->personaje)
                                        <p><strong>Personaje:</strong> {{ $producto->figura->personaje }}</p>
                                    @endif
                                    
                                    @if($producto->figura->serie)
                                        <p><strong>Serie:</strong> {{ $producto->figura->serie }}</p>
                                    @endif
                                    
                                    @if($producto->figura->artista)
                                        <p><strong>Artista:</strong> {{ $producto->figura->artista }}</p>
                                    @endif
                                    
                                    @if($producto->figura->edicion_limitada)
                                        <p><strong>Edición Limitada:</strong> Sí</p>
                                        
                                        @if($producto->figura->numero_serie)
                                            <p><strong>Número de Serie:</strong> {{ $producto->figura->numero_serie }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if(Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador')
                        <div class="mb-4">
                            <h3 class="border-bottom pb-2">Información de Proveedores</h3>
                            
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Proveedor</th>
                                        <th>Precio</th>
                                        <th>Tiempo de Entrega</th>
                                        <th>Principal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($producto->proveedores as $proveedor)
                                        <tr>
                                            <td>{{ $proveedor->nombre_empresa }}</td>
                                            <td>${{ number_format($proveedor->pivot->precio_proveedor, 2) }}</td>
                                            <td>{{ $proveedor->pivot->tiempo_entrega_dias }} días</td>
                                            <td>
                                                @if($proveedor->pivot->es_proveedor_principal)
                                                    <span class="badge bg-success">Principal</span>
                                                @else
                                                    <span class="badge bg-secondary">Alternativo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endpush