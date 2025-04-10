@extends('layouts.master')

@section('title', 'Registrar Nuevo Producto - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="card-title mb-0">Registrar Nuevo Producto</h2>
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

                    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Tipo de Producto -->
                                <div class="mb-3">
                                    <label for="tipo_producto" class="form-label">Tipo de Producto</label>
                                    <select class="form-select @error('tipo_producto') is-invalid @enderror" id="tipo_producto" name="tipo_producto" required>
                                        <option value="">Selecciona un tipo</option>
                                        <option value="comic" {{ old('tipo_producto') == 'comic' ? 'selected' : '' }}>Cómic</option>
                                        <option value="figura" {{ old('tipo_producto') == 'figura' ? 'selected' : '' }}>Figura</option>
                                    </select>
                                    @error('tipo_producto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Código de Barras -->
                                <div class="mb-3">
                                    <label for="codigo_barras" class="form-label">Código de Barras</label>
                                    <input type="text" class="form-control @error('codigo_barras') is-invalid @enderror" id="codigo_barras" name="codigo_barras" value="{{ old('codigo_barras') }}" required>
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
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
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
                                            <option value="{{ $categoria->id_categoria }}" {{ old('id_categoria') == $categoria->id_categoria ? 'selected' : '' }}>
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
                                            <option value="{{ $editorial->id_editorial }}" {{ old('id_editorial') == $editorial->id_editorial ? 'selected' : '' }}>
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
                                <!-- Proveedor -->
                                <div class="mb-3">
                                    <label for="id_proveedor" class="form-label">Proveedor</label>
                                    <select class="form-select @error('id_proveedor') is-invalid @enderror" id="id_proveedor" name="id_proveedor" required>
                                        <option value="">Selecciona un proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id_proveedor }}" {{ old('id_proveedor') == $proveedor->id_proveedor ? 'selected' : '' }}>
                                                {{ $proveedor->nombre_empresa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_proveedor')
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
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
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
                                        <input type="number" step="0.01" class="form-control @error('precio_compra') is-invalid @enderror" id="precio_compra" name="precio_compra" value="{{ old('precio_compra') }}" required>
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
                                        <input type="number" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" id="precio_venta" name="precio_venta" value="{{ old('precio_venta') }}" required>
                                        @error('precio_venta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Moneda -->
                                <div class="mb-3">
                                    <label for="id_moneda" class="form-label">Moneda</label>
                                    <select class="form-select @error('id_moneda') is-invalid @enderror" id="id_moneda" name="id_moneda" required>
                                        @foreach($monedas as $moneda)
                                            <option value="{{ $moneda->id_moneda }}" {{ old('id_moneda') == $moneda->id_moneda ? 'selected' : '' }}>
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
                            <div class="col-md-4">
                                <!-- Stock Actual -->
                                <div class="mb-3">
                                    <label for="stock_actual" class="form-label">Stock Actual</label>
                                    <input type="number" class="form-control @error('stock_actual') is-invalid @enderror" id="stock_actual" name="stock_actual" value="{{ old('stock_actual', 0) }}" required>
                                    @error('stock_actual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Stock Mínimo -->
                                <div class="mb-3">
                                    <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                    <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', 5) }}" required>
                                    @error('stock_minimo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Stock Máximo -->
                                <div class="mb-3">
                                    <label for="stock_maximo" class="form-label">Stock Máximo</label>
                                    <input type="number" class="form-control @error('stock_maximo') is-invalid @enderror" id="stock_maximo" name="stock_maximo" value="{{ old('stock_maximo', 100) }}" required>
                                    @error('stock_maximo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Campos específicos para cómics -->
                        <div id="comic_fields" class="border rounded p-3 mb-3" style="display: none;">
                            <h4 class="mb-3">Información del Cómic</h4>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- Número de Edición -->
                                    <div class="mb-3">
                                        <label for="numero_edicion" class="form-label">Número de Edición</label>
                                        <input type="text" class="form-control @error('numero_edicion') is-invalid @enderror" id="numero_edicion" name="numero_edicion" value="{{ old('numero_edicion') }}">
                                        @error('numero_edicion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- ISBN -->
                                    <div class="mb-3">
                                        <label for="isbn" class="form-label">ISBN</label>
                                        <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn') }}">
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
                                                <option value="{{ $idioma->id_idioma }}" {{ old('id_idioma') == $idioma->id_idioma ? 'selected' : '' }}>
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
                                        <input type="text" class="form-control @error('escritor') is-invalid @enderror" id="escritor" name="escritor" value="{{ old('escritor') }}">
                                        @error('escritor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Ilustrador -->
                                    <div class="mb-3">
                                        <label for="ilustrador" class="form-label">Ilustrador</label>
                                        <input type="text" class="form-control @error('ilustrador') is-invalid @enderror" id="ilustrador" name="ilustrador" value="{{ old('ilustrador') }}">
                                        @error('ilustrador')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Fecha de Publicación -->
                                    <div class="mb-3">
                                        <label for="fecha_publicacion" class="form-label">Fecha de Publicación</label>
                                        <input type="date" class="form-control @error('fecha_publicacion') is-invalid @enderror" id="fecha_publicacion" name="fecha_publicacion" value="{{ old('fecha_publicacion') }}">
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
                                        <input type="number" class="form-control @error('numero_paginas') is-invalid @enderror" id="numero_paginas" name="numero_paginas" value="{{ old('numero_paginas') }}">
                                        @error('numero_paginas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos específicos para figuras -->
                        <div id="figura_fields" class="border rounded p-3 mb-3" style="display: none;">
                            <h4 class="mb-3">Información de la Figura</h4>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- Material -->
                                    <div class="mb-3">
                                        <label for="material" class="form-label">Material</label>
                                        <input type="text" class="form-control @error('material') is-invalid @enderror" id="material" name="material" value="{{ old('material') }}">
                                        @error('material')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Altura -->
                                    <div class="mb-3">
                                        <label for="altura" class="form-label">Altura (cm)</label>
                                        <input type="number" step="0.01" class="form-control @error('altura') is-invalid @enderror" id="altura" name="altura" value="{{ old('altura') }}">
                                        @error('altura')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Peso -->
                                    <div class="mb-3">
                                        <label for="peso" class="form-label">Peso (gr)</label>
                                        <input type="number" step="0.01" class="form-control @error('peso') is-invalid @enderror" id="peso" name="peso" value="{{ old('peso') }}">
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
                                        <input type="text" class="form-control @error('escala') is-invalid @enderror" id="escala" name="escala" value="{{ old('escala') }}">
                                        @error('escala')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Personaje -->
                                    <div class="mb-3">
                                        <label for="personaje" class="form-label">Personaje</label>
                                        <input type="text" class="form-control @error('personaje') is-invalid @enderror" id="personaje" name="personaje" value="{{ old('personaje') }}">
                                        @error('personaje')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Serie -->
                                    <div class="mb-3">
                                        <label for="serie" class="form-label">Serie</label>
                                        <input type="text" class="form-control @error('serie') is-invalid @enderror" id="serie" name="serie" value="{{ old('serie') }}">
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
                                        <input type="text" class="form-control @error('artista') is-invalid @enderror" id="artista" name="artista" value="{{ old('artista') }}">
                                        @error('artista')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Edición Limitada -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="edicion_limitada" name="edicion_limitada" value="1" {{ old('edicion_limitada') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edicion_limitada">Edición Limitada</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Número de Serie -->
                                    <div class="mb-3">
                                        <label for="numero_serie" class="form-label">Número de Serie</label>
                                        <input type="text" class="form-control @error('numero_serie') is-invalid @enderror" id="numero_serie" name="numero_serie" value="{{ old('numero_serie') }}">
                                        @error('numero_serie')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Proveedor -->
                        <div class="border rounded p-3 mb-3">
                            <h4 class="mb-3">Información del Proveedor</h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Precio del Proveedor -->
                                    <div class="mb-3">
                                        <label for="precio_proveedor" class="form-label">Precio del Proveedor</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control @error('precio_proveedor') is-invalid @enderror" id="precio_proveedor" name="precio_proveedor" value="{{ old('precio_proveedor') }}" required>
                                            @error('precio_proveedor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Tiempo de Entrega (días) -->
                                    <div class="mb-3">
                                        <label for="tiempo_entrega_dias" class="form-label">Tiempo de Entrega (días)</label>
                                        <input type="number" class="form-control @error('tiempo_entrega_dias') is-invalid @enderror" id="tiempo_entrega_dias" name="tiempo_entrega_dias" value="{{ old('tiempo_entrega_dias', 7) }}">
                                        @error('tiempo_entrega_dias')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="es_proveedor_principal" name="es_proveedor_principal" value="1" {{ old('es_proveedor_principal', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="es_proveedor_principal">Es Proveedor Principal</label>
                                </div>
                            </div>
                        </div>

                        <!-- Imágenes del Producto -->
                        <div class="border rounded p-3 mb-3">
                            <h4 class="mb-3">Imágenes del Producto</h4>
                            
                            <div class="mb-3">
                                <label for="imagenes" class="form-label">Subir Imágenes</label>
                                <input type="file" class="form-control @error('imagenes.*') is-invalid @enderror" id="imagenes" name="imagenes[]" multiple accept="image/*">
                                <div class="form-text">Puedes seleccionar múltiples imágenes. Formatos aceptados: JPG, PNG.</div>
                                @error('imagenes.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="imagen_principal" class="form-label">Imagen Principal (número de orden, empezando por 0)</label>
                                <input type="number" class="form-control @error('imagen_principal') is-invalid @enderror" id="imagen_principal" name="imagen_principal" value="{{ old('imagen_principal', 0) }}" min="0">
                                @error('imagen_principal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Registrar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mostrar/ocultar campos según tipo de producto
    document.addEventListener('DOMContentLoaded', function() {
        const tipoProductoSelect = document.getElementById('tipo_producto');
        const comicFields = document.getElementById('comic_fields');
        const figuraFields = document.getElementById('figura_fields');
        
        function toggleFields() {
            if (tipoProductoSelect.value === 'comic') {
                comicFields.style.display = 'block';
                figuraFields.style.display = 'none';
            } else if (tipoProductoSelect.value === 'figura') {
                comicFields.style.display = 'none';
                figuraFields.style.display = 'block';
            } else {
                comicFields.style.display = 'none';
                figuraFields.style.display = 'none';
            }
        }
        
        // Inicializar
        toggleFields();
        
        // Escuchar cambios
        tipoProductoSelect.addEventListener('change', toggleFields);
    });
</script>
@endpush