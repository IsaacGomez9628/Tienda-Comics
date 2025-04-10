<?php

use App\Http\Controllers\Auth\AuthController as AuthAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EditorialController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\MovimientoController;

// Rutas de autenticación
Route::get('/', [AuthAuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/', [AuthAuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthAuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil de usuario
    Route::get('/perfil', [UsuarioController::class, 'editPerfil'])->name('perfil.edit');
    Route::put('/perfil', [UsuarioController::class, 'updatePerfil'])->name('perfil.update');
    
    // Productos
    Route::resource('productos', ProductoController::class);
    Route::get('/productos/buscar-codigo/{codigo}', [ProductoController::class, 'buscarPorCodigo'])->name('productos.buscarPorCodigo');
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
    
    // Categorías
    Route::resource('categorias', CategoriaController::class);
    
    // Editoriales
    Route::resource('editoriales', EditorialController::class);
    
    // Clientes
    Route::resource('clientes', ClienteController::class);
    Route::get('/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    
    // Ventas
    Route::resource('ventas', VentaController::class);
    Route::put('/ventas/{venta}/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar');
    Route::get('/ventas/{venta}/imprimir', [VentaController::class, 'imprimir'])->name('ventas.imprimir');
    
    // Proveedores
    Route::resource('proveedores', ProveedorController::class);
    Route::get('/get-ciudades/{idEstado}', [ProveedorController::class, 'getCiudades'])->name('getCiudades');
    
    // Pedidos
    Route::resource('pedidos', PedidoController::class);
    Route::put('/pedidos/{pedido}/recibir', [PedidoController::class, 'recibirPedido'])->name('pedidos.recibir');
    Route::put('/pedidos/{pedido}/cancelar', [PedidoController::class, 'cancelar'])->name('pedidos.cancelar');
    Route::post('/pedidos/{pedido}/enviar-correo', [PedidoController::class, 'enviarCorreo'])->name('pedidos.enviarCorreo');
    Route::post('/pedidos/{pedido}/registrar-respuesta', [PedidoController::class, 'registrarRespuesta'])->name('pedidos.registrarRespuesta');
    
    // Rutas solo para administradores
    Route::middleware(['admin'])->group(function () {
        // Usuarios (Empleados)
        Route::resource('usuarios', UsuarioController::class);
        
        // Membresías
        Route::resource('membresias', MembresiaController::class);
        
        // Movimientos (Historial)
        Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
        Route::get('/movimientos/{movimiento}', [MovimientoController::class, 'show'])->name('movimientos.show');
        
        // Reportes
        Route::get('/reportes/ventas', [DashboardController::class, 'reporteVentas'])->name('reportes.ventas');
        Route::get('/reportes/inventario', [DashboardController::class, 'reporteInventario'])->name('reportes.inventario');
        Route::get('/reportes/clientes', [DashboardController::class, 'reporteClientes'])->name('reportes.clientes');
    });
});