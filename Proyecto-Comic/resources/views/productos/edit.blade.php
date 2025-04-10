@extends('layouts.master')

@section('title', 'Editar Producto - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h2 class="card-title mb-0">Actualizar información del Producto</h2>
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

                    <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Tipo de Producto (solo mostrar, no editable) -->
                                <div class="mb-3">
                                    <label for="tipo_producto_display" class="form-label">Tipo de Producto</label>
                                    <input type="text" class="form-control" id="tipo_producto_display" value="{{ $producto->tipo_producto == 'comic' ? 'Cómic' : 'Figura' }}" readonly>
                                    <input type="hidden" name="tipo_producto" value="{{ $producto->tipo_producto }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Código de Barras -->
                                <div class="mb-3">
                                    <label for="codigo_barras" class="form-label">Código de Barras</label>
                                    <input type="text" class="form-control @error('codigo_barras') is-invalid @enderror" id="codigo_barras" name="codigo_barras" value="{{ old('codigo_barras', $producto->codigo_barras) }}" required>
                                    @error('codigo_barras')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Nombre del Producto -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Producto</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Categoría -->
                                <div class="mb-3">
                                    <label for="id_categoria" class="form-label">Categoría</label>
                                    <select class="form-select @error('id_categoria') is-invalid @enderror" id="id_categoria" name="id_categoria" required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id_categoria }}" {{ old('id_categoria', $producto->id_categoria) == $categoria->id_categoria ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_categoria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Editorial -->
                                <div class="mb-3">
                                    <label for="id_editorial" class="form-label">Editorial</label>
                                    <select class="form-select @error('id_editorial') is-invalid @enderror" id="id_editorial" name="id_editorial" required>
                                        <option value="">Selecciona una editorial</option>
                                        @foreach($editoriales as $editorial)
                                            <option value="{{ $editorial->id_editorial }}" {{ old('id_editorial', $producto->id_editorial) == $editorial->id_editorial ? 'selected' : '' }}>
                                                {{ $editorial->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_editorial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Moneda -->
                                <div class="mb-3">
                                    <label for="id_moneda" class="form-label">Moneda</label>
                                    <select class="form-select @error('id_moneda') is-invalid @enderror" id="id_moneda" name="id_moneda" required>
                                        @foreach($monedas as $moneda)
                                            <option value="{{ $moneda->id_moneda }}" {{ old('id_moneda', $producto->id_moneda) == $moneda->id_moneda ? 'selected' : '' }}>
                                                {{ $moneda->nombre }} ({{ $moneda->simbolo }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_moneda')
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
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <!-- Precio de Compra -->
                                <div class="mb-3">
                                    <label for="precio_compra" class="form-label">Precio de Compra</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('precio_compra') is-invalid @enderror" id="precio_compra" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra) }}" required>
                                        @error('precio_compra')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Precio de Venta -->
                                <div class="mb-3">
                                    <label for="precio_venta" class="form-label">Precio de Venta</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" id="precio_venta" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
                                        @error('precio_venta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <!-- Stock Actual -->
                                <div class="mb-3">
                                    <label for="stock_actual" class="form-label">Stock Actual</label>
                                    <input type="number" class="form-control @error('stock_actual') is-invalid @enderror" id="stock_actual" name="stock_actual" value="{{ old('stock_actual', $producto->stock_actual) }}" required>
                                    @error('stock_actual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Stock Mínimo -->
                                <div class="mb-3">
                                    <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                    <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required>
                                    @error('stock_minimo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Stock Máximo -->
                                <div class="mb-3">
                                    <label for="stock_maximo" class="form-label">Stock Máximo</label>
                                    <input type="number" class="form-control @error('stock_maximo') is-invalid @enderror" id="stock_maximo" name="stock_maximo" value="{{ old('stock_maximo', $producto->stock_maximo) }}" required>
                                    @error('stock_maximo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if($producto->tipo_producto == 'comic')
                            <!-- Campos específicos para cómics -->
                            <div class="border rounded p-3 mb-3">
                                <h4 class="mb-3">Información del Cómic</h4>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- Número de Edición -->
                                        <div class="mb-3">
                                            <label for="numero_edicion" class="form-label">Número de Edición</label>
                                            <input type="text" class="form-control @error('numero_edicion') is-invalid @enderror" id="numero_edicion" name="numero_edicion" value="{{ old('numero_edicion', $producto->comic->numero_edicion ?? '') }}">
                                            @error('numero_edicion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- ISBN -->
                                        <div class="mb-3">
                                            <label for="isbn" class="form-label">ISBN</label>
                                            <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn', $producto->comic->isbn ?? '') }}">
                                            @error('isbn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Idioma -->
                                        <div class="mb-3">
                                            <label for="id_idioma" class="form-label">Idioma</label>
                                            <select class="form-select @error('id_idioma') is-invalid @enderror" id="id_idioma" name="id_idioma">
                                                <option value="">Selecciona un idioma</option>
                                                @foreach($idiomas as $idioma)
                                                    <option value="{{ $idioma->id_idioma }}" {{ old('id_idioma', $producto->comic->id_idioma ?? '') == $idioma->id_idioma ? 'selected' : '' }}>
                                                        {{ $idioma->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_idioma')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- Escritor -->
                                        <div class="mb-3">
                                            <label for="escritor" class="form-label">Escritor</label>
                                            <input type="text" class="form-control @error('escritor') is-invalid @enderror" id="escritor" name="escritor" value="{{ old('escritor', $producto->comic->escritor ?? '') }}">
                                            @error('escritor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Ilustrador -->
                                        <div class="mb-3">
                                            <label for="ilustrador" class="form-label">Ilustrador</label>
                                            <input type="text" class="form-control @error('ilustrador') is-invalid @enderror" id="ilustrador" name="ilustrador" value="{{ old('ilustrador', $producto->comic->ilustrador ?? '') }}">
                                            @error('ilustrador')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Fecha de Publicación -->
                                        <div class="mb-3">
                                            <label for="fecha_publicacion" class="form-label">Fecha de Publicación</label>
                                            <input type="date" class="form-control @error('fecha_publicacion') is-invalid @enderror" id="fecha_publicacion" name="fecha_publicacion" value="{{ old('fecha_publicacion', $producto->comic->fecha_publicacion ? $producto->comic->fecha_publicacion->format('Y-m-d') : '') }}">
                                            @error('fecha_publicacion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- Número de Páginas -->
                                        <div class="mb-3">
                                            <label for="numero_paginas" class="form-label">Número de Páginas</label>
                                            <input type="number" class="form-control @error('numero_paginas') is-invalid @enderror" id="numero_paginas" name="numero_paginas" value="{{ old('numero_paginas', $producto->comic->numero_paginas ?? '') }}">
                                            @error('numero_paginas')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($producto->tipo_producto == 'figura')
                            <!-- Campos específicos para figuras -->
                            <div class="border rounded p-3 mb-3">
                                <h4 class="mb-3">Información de la Figura</h4>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- Material -->
                                        <div class="mb-3">
                                            <label for="material" class="form-label">Material</label>
                                            <input type="text" class="form-control @error('material') is-invalid @enderror" id="material" name="material" value="{{ old('material', $producto->figura->material ?? '') }}">
                                            @error('material')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Altura -->
                                        <div class="mb-3">
                                            <label for="altura" class="form-label">Altura (cm)</label>
                                            <input type="number" step="0.01" class="form-control @error('altura') is-invalid @enderror" id="altura" name="altura" value="{{ old('altura', $producto->figura->altura ?? '') }}">
                                            @error('altura')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Peso -->
                                        <div class="mb-3">
                                            <label for="peso" class="form-label">Peso (gr)</label>
                                            <input type="number" step="0.01" class="form-control @error('peso') is-invalid @enderror" id="peso" name="peso" value="{{ old('peso', $producto->figura->peso ?? '') }}">
                                            @error('peso')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- Escala -->
                                        <div class="mb-3">
                                            <label for="escala" class="form-label">Escala</label>
                                            <input type="text" class="form-control @error('escala') is-invalid @enderror" id="escala" name="escala" value="{{ old('escala', $producto->figura->escala ?? '') }}">
                                            @error('escala')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Personaje -->
                                        <div class="mb-3">
                                            <label for="personaje" class="form-label">Personaje</label>
                                            <input type="text" class="form-control @error('personaje') is-invalid @enderror" id="personaje" name="personaje" value="{{ old('personaje', $producto->figura->personaje ?? '') }}">
                                            @error('personaje')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Serie -->
                                        <div class="mb-3">
                                            <label for="serie" class="form-label">Serie</label>
                                            <input type="text" class="form-control @error('serie') is-invalid @enderror" id="serie" name="serie" value="{{ old('serie', $producto->figura->serie ?? '') }}">
                                            @error('serie')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <!-- Artista -->
                                        <div class="mb-3">
                                            <label for="artista" class="form-label">Artista</label>
                                            <input type="text" class="form-control @error('artista') is-invalid @enderror" id="artista" name="artista" value="{{ old('artista', $producto->figura->artista ?? '') }}">
                                            @error('artista')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Edición Limitada -->
                                        <div class="mb-3">
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" id="edicion_limitada" name="edicion_limitada" value="1" {{ old('edicion_limitada', $producto->figura->edicion_limitada ?? 0) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="edicion_limitada">Edición Limitada</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Número de Serie -->
                                        <div class="mb-3">
                                            <label for="numero_serie" class="form-label">Número de Serie</label>
                                            <input type="text" class="form-control @error('numero_serie') is-invalid @enderror" id="numero_serie" name="numero_serie" value="{{ old('numero_serie', $producto->figura->numero_serie ?? '') }}">
                                            @error('numero_serie')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Imágenes Actuales -->
                        @if(count($imagenes) > 0)
                            <div class="border rounded p-3 mb-3">
                                <h4 class="mb-3">Imágenes Actuales</h4>
                                
                                <div class="row">
                                    @foreach($imagenes as $imagen)
                                        <div class="col-md-3 mb-3">
                                            <div class="card h-100">
                                                <img src="{{ asset('storage/' . $imagen->ruta) }}" class="card-img-top" alt="{{ $producto->nombre }}" style="height: 150px; object-fit: cover;">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="imagen_principal" id="imagen_{{ $imagen->id_imagen }}" value="{{ $imagen->id_imagen }}" {{ $imagen->es_principal ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="imagen_{{ $imagen->id_imagen }}">
                                                            Imagen Principal
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Nuevas Imágenes -->
                        <div class="border rounded p-3 mb-3">
                            <h4 class="mb-3">Agregar Nuevas Imágenes</h4>
                            
                            <div class="mb-3">
                                <label for="imagenes" class="form-label">Subir Imágenes</label>
                                <input type="file" class="form-control @error('imagenes.*') is-invalid @enderror" id="imagenes" name="imagenes[]" multiple accept="image/*">
                                <div class="form-text">Puedes seleccionar múltiples imágenes para agregar. Formatos aceptados: JPG, PNG.</div>
                                @error('imagenes.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection