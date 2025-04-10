@extends('layouts.master')

@section('title', 'Registro de Proveedor - Tienda de Cómics')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="card-title mb-0">Registro de Proveedor</h2>
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

                    <form action="{{ route('proveedores.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h4 class="border-bottom pb-2 mb-3">Información de la Empresa</h4>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Nombre de la Empresa -->
                                <div class="mb-3">
                                    <label for="nombre_empresa" class="form-label">Nombre de la Empresa</label>
                                    <input type="text" class="form-control @error('nombre_empresa') is-invalid @enderror" id="nombre_empresa" name="nombre_empresa" value="{{ old('nombre_empresa') }}" required>
                                    @error('nombre_empresa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Teléfono de la Empresa -->
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono de la Empresa</label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono') }}" required minlength="10">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Email de la Empresa -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico de la Empresa</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Página Web -->
                                <div class="mb-3">
                                    <label for="pagina_web" class="form-label">Página Web</label>
                                    <input type="url" class="form-control @error('pagina_web') is-invalid @enderror" id="pagina_web" name="pagina_web" value="{{ old('pagina_web') }}">
                                    @error('pagina_web')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h4 class="border-bottom pb-2 mb-3">Información de Contacto</h4>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Nombre del Contacto -->
                                <div class="mb-3">
                                    <label for="nombre_contacto" class="form-label">Nombre del Responsable</label>
                                    <input type="text" class="form-control @error('nombre_contacto') is-invalid @enderror" id="nombre_contacto" name="nombre_contacto" value="{{ old('nombre_contacto') }}">
                                    @error('nombre_contacto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Apellido Paterno del Contacto -->
                                <div class="mb-3">
                                    <label for="apellido_paterno_contacto" class="form-label">Apellido Paterno del Responsable</label>
                                    <input type="text" class="form-control @error('apellido_paterno_contacto') is-invalid @enderror" id="apellido_paterno_contacto" name="apellido_paterno_contacto" value="{{ old('apellido_paterno_contacto') }}">
                                    @error('apellido_paterno_contacto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Apellido Materno del Contacto -->
                                <div class="mb-3">
                                    <label for="apellido_materno_contacto" class="form-label">Apellido Materno del Responsable</label>
                                    <input type="text" class="form-control @error('apellido_materno_contacto') is-invalid @enderror" id="apellido_materno_contacto" name="apellido_materno_contacto" value="{{ old('apellido_materno_contacto') }}">
                                    @error('apellido_materno_contacto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Teléfono Personal -->
                                <div class="mb-3">
                                    <label for="telefono_personal" class="form-label">Teléfono Personal</label>
                                    <input type="text" class="form-control @error('telefono_personal') is-invalid @enderror" id="telefono_personal" name="telefono_personal" value="{{ old('telefono_personal') }}" minlength="10">
                                    @error('telefono_personal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h4 class="border-bottom pb-2 mb-3">Dirección</h4>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Estado -->
                                <div class="mb-3">
                                    <label for="id_estado" class="form-label">Estado</label>
                                    <select class="form-select @error('id_estado') is-invalid @enderror" id="id_estado" name="id_estado">
                                        <option value="">Selecciona un estado</option>
                                        @foreach($estados as $estado)
                                            <option value="{{ $estado->id_estado }}" {{ old('id_estado') == $estado->id_estado ? 'selected' : '' }}>
                                                {{ $estado->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Ciudad -->
                                <div class="mb-3">
                                    <label for="id_ciudad" class="form-label">Ciudad</label>
                                    <select class="form-select @error('id_ciudad') is-invalid @enderror" id="id_ciudad" name="id_ciudad" disabled>
                                        <option value="">Selecciona primero un estado</option>
                                    </select>
                                    @error('id_ciudad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <!-- Calle -->
                                <div class="mb-3">
                                    <label for="calle" class="form-label">Calle</label>
                                    <input type="text" class="form-control @error('calle') is-invalid @enderror" id="calle" name="calle" value="{{ old('calle') }}">
                                    @error('calle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Número Exterior -->
                                <div class="mb-3">
                                    <label for="numero_exterior" class="form-label">Número Exterior</label>
                                    <input type="text" class="form-control @error('numero_exterior') is-invalid @enderror" id="numero_exterior" name="numero_exterior" value="{{ old('numero_exterior') }}">
                                    @error('numero_exterior')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- Número Interior -->
                                <div class="mb-3">
                                    <label for="numero_interior" class="form-label">Número Interior</label>
                                    <input type="text" class="form-control @error('numero_interior') is-invalid @enderror" id="numero_interior" name="numero_interior" value="{{ old('numero_interior') }}">
                                    @error('numero_interior')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <!-- Colonia -->
                                <div class="mb-3">
                                    <label for="colonia" class="form-label">Colonia</label>
                                    <input type="text" class="form-control @error('colonia') is-invalid @enderror" id="colonia" name="colonia" value="{{ old('colonia') }}">
                                    @error('colonia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Código Postal -->
                                <div class="mb-3">
                                    <label for="codigo_postal" class="form-label">Código Postal</label>
                                    <input type="text" class="form-control @error('codigo_postal') is-invalid @enderror" id="codigo_postal" name="codigo_postal" value="{{ old('codigo_postal') }}">
                                    @error('codigo_postal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <!-- Referencias -->
                                <div class="mb-3">
                                    <label for="referencias" class="form-label">Referencias</label>
                                    <textarea class="form-control @error('referencias') is-invalid @enderror" id="referencias" name="referencias" rows="2">{{ old('referencias') }}</textarea>
                                    @error('referencias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <!-- Notas -->
                                <div class="mb-3">
                                    <label for="notas" class="form-label">Notas Adicionales</label>
                                    <textarea class="form-control @error('notas') is-invalid @enderror" id="notas" name="notas" rows="3">{{ old('notas') }}</textarea>
                                    @error('notas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('proveedores.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Registrar Proveedor</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const estadoSelect = document.getElementById('id_estado');
        const ciudadSelect = document.getElementById('id_ciudad');
        
        // Function to load cities based on selected state
        function loadCities(estadoId) {
            if (!estadoId) {
                ciudadSelect.innerHTML = '<option value="">Selecciona primero un estado</option>';
                ciudadSelect.disabled = true;
                return;
            }
            
            // Enable city selection
            ciudadSelect.disabled = false;
            
            // Show loading option
            ciudadSelect.innerHTML = '<option value="">Cargando ciudades...</option>';
            
            // Fetch cities from server
            fetch(`{{ url('get-ciudades') }}/${estadoId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                // Create options for each city
                let options = '<option value="">Selecciona una ciudad</option>';
                
                data.forEach(ciudad => {
                    const selected = ciudad.id_ciudad == {{ old('id_ciudad', 0) }} ? 'selected' : '';
                    options += `<option value="${ciudad.id_ciudad}" ${selected}>${ciudad.nombre}</option>`;
                });
                
                ciudadSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Error cargando ciudades:', error);
                ciudadSelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
            });
        }
        
        // Handle state selection change
        estadoSelect.addEventListener('change', function() {
            loadCities(this.value);
        });
        
        // Initial load if a state is selected
        if (estadoSelect.value) {
            loadCities(estadoSelect.value);
        }
    });
</script>
@endpush