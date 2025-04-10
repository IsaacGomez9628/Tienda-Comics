@extends('layouts.master')

@section('title', 'Nuevo Pedido a Proveedor - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="card-title mb-0">Levantamiento de Pedido a Proveedor</h2>
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

                    <form action="{{ route('pedidos.store') }}" method="POST" id="pedidoForm">
                        @csrf
                        <input type="hidden" name="tipo_pedido" value="proveedor">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Proveedor -->
                                <div class="mb-3">
                                    <label for="id_proveedor" class="form-label">Proveedor</label>
                                    <select class="form-select select2 @error('id_proveedor') is-invalid @enderror" id="id_proveedor" name="id_proveedor" required>
                                        <option value="">Selecciona un proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id_proveedor }}" {{ old('id_proveedor', $proveedor_id ?? '') == $proveedor->id_proveedor ? 'selected' : '' }}>
                                                {{ $proveedor->nombre_empresa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_proveedor')
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
                                            <option value="{{ $moneda->id_moneda }}" {{ old('id_moneda', 1) == $moneda->id_moneda ? 'selected' : '' }}>
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
                            <div class="col-md-6">
                                <!-- Fecha de Entrega Estimada -->
                                <div class="mb-3">
                                    <label for="fecha_entrega_estimada" class="form-label">Fecha de Entrega Estimada</label>
                                    <input type="date" class="form-control @error('fecha_entrega_estimada') is-invalid @enderror" id="fecha_entrega_estimada" name="fecha_entrega_estimada" value="{{ old('fecha_entrega_estimada') }}">
                                    @error('fecha_entrega_estimada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Estado del Pedido -->
                                <div class="mb-3">
                                    <label for="id_estado_pedido" class="form-label">Estado del Pedido</label>
                                    <select class="form-select @error('id_estado_pedido') is-invalid @enderror" id="id_estado_pedido" name="id_estado_pedido" required>
                                        @foreach($estadosPedido as $estado)
                                            <option value="{{ $estado->id_estado_pedido }}" {{ old('id_estado_pedido', 1) == $estado->id_estado_pedido ? 'selected' : '' }}>
                                                {{ $estado->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_estado_pedido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Búsqueda de Productos -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">Agregar Productos</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-5">
                                        <select class="form-select select2-productos" id="producto_selector">
                                            <option value="">Buscar producto (código o nombre)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control" id="cantidad_selector" min="1" value="1" placeholder="Cantidad">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" id="precio_selector" min="0" value="0" placeholder="Precio">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" id="agregar_producto">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabla de Productos -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">Productos del Pedido</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="productos_tabla">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Precio Unitario</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="empty_row">
                                                <td colspan="5" class="text-center">No se han agregado productos</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                                <td colspan="2"><span id="subtotal">$0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Impuesto:</strong></td>
                                                <td colspan="2"><span id="impuesto">$0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                <td colspan="2"><span id="total">$0.00</span></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notas -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="notas" class="form-label">Notas</label>
                                    <textarea class="form-control @error('notas') is-invalid @enderror" id="notas" name="notas" rows="3">{{ old('notas') }}</textarea>
                                    @error('notas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('pedidos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="submit_pedido" disabled>
                                <i class="fas fa-check"></i> Guardar Pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let productos = [];
        let subtotalGeneral = 0;
        let impuestoGeneral = 0;
        let totalGeneral = 0;
        const TASA_IMPUESTO = 0.16; // 16% de IVA
        
        // Inicializar Select2 para proveedor
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione un proveedor',
            allowClear: true
        });
        
        // Inicializar Select2 para productos
        $('.select2-productos').select2({
            theme: 'bootstrap-5',
            placeholder: 'Buscar producto por nombre o código',
            allowClear: true,
            ajax: {
                url: '{{ route('productos.buscar') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        }).on('select2:select', function(e) {
            const productoData = e.params.data;
            
            if (productoData && productoData.precio) {
                document.getElementById('precio_selector').value = productoData.precio;
            }
        });
        
        // Agregar producto a la tabla
        document.getElementById('agregar_producto').addEventListener('click', function() {
            const productoSelector = document.getElementById('producto_selector');
            const cantidadSelector = document.getElementById('cantidad_selector');
            const precioSelector = document.getElementById('precio_selector');
            
            if (!productoSelector.value) {
                alert('Por favor, seleccione un producto');
                return;
            }
            
            const cantidad = parseInt(cantidadSelector.value);
            
            if (isNaN(cantidad) || cantidad < 1) {
                alert('La cantidad debe ser un número mayor a 0');
                return;
            }
            
            const precio = parseFloat(precioSelector.value);
            
            if (isNaN(precio) || precio < 0) {
                alert('El precio debe ser un número mayor o igual a 0');
                return;
            }
            
            const productoData = $('#producto_selector').select2('data')[0];
            
            if (productoData && productoData.id) {
                agregarProductoTabla({
                    id: productoData.id,
                    nombre: productoData.text,
                    precio: precio,
                    cantidad: cantidad
                });
                
                // Limpiar selección
                $('#producto_selector').val(null).trigger('change');
                cantidadSelector.value = 1;
                precioSelector.value = 0;
            }
        });
        
        // Función para agregar producto a la tabla
        function agregarProductoTabla(producto) {
            // Verificar si el producto ya existe en la tabla
            const productoExistente = productos.find(p => p.id === producto.id);
            
            if (productoExistente) {
                // Actualizar cantidad del producto existente
                const nuevaCantidad = productoExistente.cantidad + producto.cantidad;
                productoExistente.precio = producto.precio; // Actualizar precio
                productoExistente.cantidad = nuevaCantidad;
                productoExistente.subtotal = productoExistente.precio * nuevaCantidad;
                
                // Actualizar fila existente
                const filaExistente = document.querySelector(`tr[data-producto-id="${producto.id}"]`);
                filaExistente.querySelector('.precio-unitario').textContent = `$${producto.precio.toFixed(2)}`;
                filaExistente.querySelector('.cantidad-producto').textContent = nuevaCantidad;
                filaExistente.querySelector('.subtotal-producto').textContent = `$${productoExistente.subtotal.toFixed(2)}`;
            } else {
                // Calcular subtotal
                const subtotal = producto.precio * producto.cantidad;
                
                // Agregar a la lista de productos
                productos.push({
                    id: producto.id,
                    nombre: producto.nombre,
                    precio: producto.precio,
                    cantidad: producto.cantidad,
                    subtotal: subtotal
                });
                
                // Ocultar fila vacía si es necesario
                const emptyRow = document.getElementById('empty_row');
                if (emptyRow) {
                    emptyRow.style.display = 'none';
                }
                
                // Crear nueva fila
                const tabla = document.getElementById('productos_tabla');
                const tbody = tabla.querySelector('tbody');
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-producto-id', producto.id);
                
                newRow.innerHTML = `
                    <td>${producto.nombre}</td>
                    <td class="precio-unitario">$${producto.precio.toFixed(2)}</td>
                    <td class="cantidad-producto">${producto.cantidad}</td>
                    <td class="subtotal-producto">$${subtotal.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger eliminar-producto" data-id="${producto.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(newRow);
                
                // Agregar evento para eliminar
                newRow.querySelector('.eliminar-producto').addEventListener('click', function() {
                    eliminarProducto(producto.id);
                });
            }
            
            // Crear inputs ocultos para el formulario
            actualizarInputsProductos();
            
            // Recalcular totales
            recalcularTotales();
        }
        
        // Función para eliminar producto
        function eliminarProducto(productoId) {
            // Eliminar de la lista
            productos = productos.filter(p => p.id !== productoId);
            
            // Eliminar fila de la tabla
            const fila = document.querySelector(`tr[data-producto-id="${productoId}"]`);
            if (fila) {
                fila.remove();
            }
            
            // Mostrar fila vacía si no hay productos
            if (productos.length === 0) {
                document.getElementById('empty_row').style.display = '';
            }
            
            // Actualizar inputs y totales
            actualizarInputsProductos();
            recalcularTotales();
        }
        
        // Función para actualizar inputs ocultos
        function actualizarInputsProductos() {
            // Eliminar inputs existentes
            const prevInputs = document.querySelectorAll('.producto-input');
            prevInputs.forEach(input => input.remove());
            
            // Crear nuevos inputs
            const form = document.getElementById('pedidoForm');
            
            productos.forEach((producto, index) => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = `productos[${index}][id]`;
                idInput.value = producto.id;
                idInput.className = 'producto-input';
                form.appendChild(idInput);
                
                const cantidadInput = document.createElement('input');
                cantidadInput.type = 'hidden';
                cantidadInput.name = `productos[${index}][cantidad]`;
                cantidadInput.value = producto.cantidad;
                cantidadInput.className = 'producto-input';
                form.appendChild(cantidadInput);
                
                const precioInput = document.createElement('input');
                precioInput.type = 'hidden';
                precioInput.name = `productos[${index}][precio]`;
                precioInput.value = producto.precio;
                precioInput.className = 'producto-input';
                form.appendChild(precioInput);
            });
            
            // Habilitar/deshabilitar botón de guardar
            document.getElementById('submit_pedido').disabled = productos.length === 0;
        }
        
        // Función para recalcular totales
        function recalcularTotales() {
            subtotalGeneral = productos.reduce((total, producto) => total + producto.subtotal, 0);
            impuestoGeneral = subtotalGeneral * TASA_IMPUESTO;
            totalGeneral = subtotalGeneral + impuestoGeneral;
            
            // Actualizar elementos HTML
            document.getElementById('subtotal').textContent = `$${subtotalGeneral.toFixed(2)}`;
            document.getElementById('impuesto').textContent = `$${impuestoGeneral.toFixed(2)}`;
            document.getElementById('total').textContent = `$${totalGeneral.toFixed(2)}`;
        }
    });
</script>
@endpush