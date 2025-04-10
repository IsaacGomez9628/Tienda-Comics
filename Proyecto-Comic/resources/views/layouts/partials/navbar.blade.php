@php
    $isAdmin = Auth::user()->usuario && Auth::user()->usuario->rol && Auth::user()->usuario->rol->nombre === 'Administrador';
@endphp

<!-- Navbar Administrador -->
<nav class="navbar navbar-expand-lg {{ $isAdmin ? 'navbar-dark bg-dark' : 'navbar-dark bg-secondary' }}">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Tienda Cómics</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                @if($isAdmin)
                    <!-- Admin Navigation -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Productos</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('productos.index') }}">Stock Tienda</a></li>
                            <li><a class="dropdown-item" href="{{ route('productos.create') }}">Registrar Producto</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Proveedores</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('proveedores.index') }}">Lista de Proveedores</a></li>
                            <li><a class="dropdown-item" href="{{ route('proveedores.create') }}">Dar de Alta Proveedor</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Pedidos</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('pedidos.create', ['tipo' => 'proveedor']) }}">Pedidos a Proveedor</a></li>
                            <li><a class="dropdown-item" href="{{ route('pedidos.create', ['tipo' => 'cliente']) }}">Pedidos a Cliente</a></li>
                            <li><a class="dropdown-item" href="{{ route('pedidos.index') }}">Pedidos Realizados</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Empleados</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('usuarios.index') }}">Lista de Empleados</a></li>
                            <li><a class="dropdown-item" href="{{ route('usuarios.create') }}">Registrar Empleado</a></li>
                        </ul>
                    </li>
                @else
                    <!-- Employee Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('productos.index') }}">Stock de Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pedidos.index') }}">Pedidos de Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('proveedores.index') }}">Lista de Proveedores</a>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Clientes</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('clientes.index') }}">Lista de Clientes</a></li>
                        <li><a class="dropdown-item" href="{{ route('clientes.create') }}">Registrar Cliente</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('ventas.create') }}">Nueva Venta</a>
                </li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('perfil.edit') }}">Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>