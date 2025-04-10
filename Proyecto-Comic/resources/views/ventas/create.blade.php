@extends('layouts.master')

@section('title', 'Registrar Venta - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h2 class="card-title mb-0">Registrar Venta</h2>
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

                    <form action="{{ route('ventas.store') }}" method="POST" id="ventaForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Cliente (opcional) -->
                                <div class="mb-3">
                                    <label for="id_cliente" class="form-label">Cliente (Opcional)</label>
                                    <select class="form-select select2 @error('id_cliente') is-invalid @enderror" id="id_cliente" name="id_cliente">
                                        <option value="">Cliente casual (sin registro)</option>
                                        @if(isset($cliente_id) && isset($cliente_nombre))
                                            <option value="{{ $cliente_id }}" selected>{{ $cliente_nombre }}</option>
                                        @endif
                                    </select>
                                    @error('id_cliente')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Método de Pago -->
                                <div class="mb-3">
                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                    <select class="form-select @error('metodo_pago') is-invalid @enderror" id="metodo_pago" name="metodo_pago" required>
                                        <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                        <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                        <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia Bancaria</option>
                                    </select>
                                    @error('metodo_pago')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- ID de Moneda (oculto) -->
                        <input type="hidden" name="id_moneda" value="1">
                        
                        <!-- Búsqueda de Productos -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">Agregar Productos</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <select class="form-select select2-productos" id="producto_selector">
                                            <option value="">Buscar producto (código o nombre)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control" id="cantidad_selector" min="1" value="1" placeholder="Cantidad">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" id="agregar_producto">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Escanear código de barras (opcional) -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="codigo_barras" placeholder="Escanear código de barras">
                                            <button class="btn btn-outline-secondary" type="button" id="buscar_codigo">
                                                <i class="fas fa-barcode"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabla de Productos Seleccionados -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">Productos Seleccionados</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="productos_tabla">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Precio</th>
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
                                            <tr id="descuento_row" style="display: none;">
                                                <td colspan="3" class="text-end"><strong>Descuento por membresía:</strong></td>
                                                <td colspan="2"><span id="descuento">$0.00</span></td>
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
                        
                        <!-- Información del cliente seleccionado -->
                        <div id="cliente_info" class="card mb-4" style="display: none;">
                            <div class="card-header bg-info text-white">
                                <h4 class="mb-0">Información del Cliente</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nombre:</strong> <span id="cliente_nombre"></span></p>
                                        <p><strong>Código:</strong> <span id="cliente_codigo"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Membresía:</strong> <span id="cliente_membresia"></span></p>
                                        <p><strong>Descuento:</strong> <span id="cliente_descuento"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-success" id="submit_venta" disabled>
                                <i class="fas fa-check"></i> Confirmar Venta
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
        let clienteData = null;
        let subtotalGeneral = 0;
        let descuentoGeneral = 0;
        let totalGeneral = 0;
        
        // Inicializar Select2 para clientes
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione un cliente',
            allowClear: true,
            ajax: {
                url: '{{ route('clientes.buscar') }}',
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
            const clienteId = e.params.data.id;
            
            // Obtener información del cliente mediante AJAX
            fetch(`{{ url('clientes') }}/${clienteId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                clienteData = data;
                
                // Actualizar información visible
                document.getElementById('cliente_nombre').textContent = clienteData.persona.nombre_completo;
                document.getElementById('cliente_codigo').textContent = clienteData.codigo_cliente;
                document.getElementById('cliente_membresia').textContent = clienteData.membresia ? clienteData.membresia.nombre : 'Sin membresía';
                document.getElementById('cliente_descuento').textContent = clienteData.membresia ? clienteData.membresia.porcentaje_descuento + '%' : '0%';
                
                // Mostrar sección de info del cliente
                document.getElementById('cliente_info').style.display = 'block';
                
                // Recalcular la venta con el descuento
                recalcularTotales();
            })
            .catch(error => {
                console.error('Error cargando datos del cliente:', error);
            });
        }).on('select2:unselect', function() {
            clienteData = null;
            document.getElementById('cliente_info').style.display = 'none';
            recalcularTotales();
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
        });
        
        // Agregar producto a la tabla
        document.getElementById('agregar_producto').addEventListener('click', function() {
            const productoSelector = document.getElementById('producto_selector');
            const cantidadSelector = document.getElementById('cantidad_selector');
            
            if (!productoSelector.value) {
                alert('Por favor, seleccione un producto');
                return;
            }
            
            const cantidad = parseInt(cantidadSelector.value);
            
            if (isNaN(cantidad) || cantidad < 1) {
                alert('La cantidad debe ser un número mayor a 0');
                return;
            }
            
            const productoData = $('#producto_selector').select2('data')[0];
            
            if (productoData && productoData.id) {
                agregarProductoTabla({
                    id: productoData.id,
                    nombre: productoData.text,
                    precio: productoData.precio,
                    moneda: productoData.moneda,
                    stock: productoData.stock,
                    cantidad: cantidad
                });
                
                // Limpiar selección
                $('#producto_selector').val(null).trigger('change');
                cantidadSelector.value = 1;
            }
        });
        
        // Buscar por código de barras
        document.getElementById('buscar_codigo').addEventListener('click', function() {
            const codigo = document.getElementById('codigo_barras').value.trim();
            
            if (!codigo) {
                alert('Por favor, ingrese un código de barras');
                return;
            }
            
            // Buscar producto por código de barras mediante AJAX
            fetch(`{{ url('productos/buscar-codigo') }}/${codigo}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Producto no encontrado');
                }
                return response.json();
            })
            .then(data => {
                agregarProductoTabla({
                    id: data.id,
                    nombre: data.nombre,
                    precio: data.precio,
                    moneda: data.moneda,
                    stock: data.stock,
                    cantidad: 1
                });
                
                // Limpiar campo
                document.getElementById('codigo_barras').value = '';
            })
            .catch(error => {
                alert('Producto no encontrado o no disponible');
                console.error('Error buscando producto:', error);
            });
        });
        
        // Función para agregar producto a la tabla
        function agregarProductoTabla(producto) {
            // Verificar si el producto ya existe en la tabla
            const productoExistente = productos.find(p => p.id === producto.id);
            
            if (productoExistente) {
                // Actualizar cantidad del producto existente
                const nuevaCantidad = productoExistente.cantidad + producto.cantidad;
                
                if (nuevaCantidad > producto.stock) {
                    alert(`No hay suficiente stock disponible. Stock actual: ${producto.stock}`);
                    return;
                }
                
                productoExistente.cantidad = nuevaCantidad;
                productoExistente.subtotal = productoExistente.precio * nuevaCantidad;
                
                // Actualizar fila existente
                const filaExistente = document.querySelector(`tr[data-producto-id="${producto.id}"]`);
                filaExistente.querySelector('.cantidad-producto').textContent = nuevaCantidad;
                filaExistente.querySelector('.subtotal-producto').textContent = `${producto.moneda}${productoExistente.subtotal.toFixed(2)}`;
            } else {
                // Verificar stock
                if (producto.cantidad > producto.stock) {
                    alert(`No hay suficiente stock disponible. Stock actual: ${producto.stock}`);
                    return;
                }
                
                // Calcular subtotal
                const subtotal = producto.precio * producto.cantidad;
                
                // Agregar a la lista de productos
                productos.push({
                    id: producto.id,
                    nombre: producto.nombre,
                    precio: producto.precio,
                    moneda: producto.moneda,
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
                    <td>${producto.moneda}${producto.precio.toFixed(2)}</td>
                    <td class="cantidad-producto">${producto.cantidad}</td>
                    <td class="subtotal-producto">${producto.moneda}${subtotal.toFixed(2)}</td>
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
            const form = document.getElementById('ventaForm');
            
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
            
            // Habilitar/deshabilitar botón de confirmar
            document.getElementById('submit_venta').disabled = productos.length === 0;
        }
        
        // Función para recalcular totales
        function recalcularTotales() {
            subtotalGeneral = productos.reduce((total, producto) => total + producto.subtotal, 0);
            
            // Calcular descuento si hay cliente con membresía
            descuentoGeneral = 0;
            if (clienteData && clienteData.membresia) {
                descuentoGeneral = subtotalGeneral * (clienteData.membresia.porcentaje_descuento / 100);
            }
            
            totalGeneral = subtotalGeneral - descuentoGeneral;
            
            // Actualizar elementos HTML
            document.getElementById('subtotal').textContent = `$${subtotalGeneral.toFixed(2)}`;
            
            const descuentoRow = document.getElementById('descuento_row');
            if (descuentoGeneral > 0) {
                descuentoRow.style.display = '';
                document.getElementById('descuento').textContent = `$${descuentoGeneral.toFixed(2)}`;
            } else {
                descuentoRow.style.display = 'none';
            }
            
            document.getElementById('total').textContent = `$${totalGeneral.toFixed(2)}`;
        }
        
        // Manejar enter en el campo de código de barras
        document.getElementById('codigo_barras').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('buscar_codigo').click();
            }
        });
    });
</script>
@endpush