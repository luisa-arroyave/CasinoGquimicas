<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CasinoController as AdminCasinoController;
use App\Http\Controllers\Admin\ConsumoManualController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\PrecioController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TipoUsuarioController;
use App\Http\Controllers\Admin\UsuarioController as AdminUsuarioController;
use App\Http\Controllers\Admin\VisitanteController;
use App\Http\Controllers\Api\ConsumoValidarQrController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CasinoEscaneoController;
use App\Http\Controllers\CasinoPanelController;
use App\Http\Controllers\CuentaCobroController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GestionHumanaController;
use App\Http\Controllers\OperativoPedidosController;
use App\Http\Controllers\SolicitarConsumoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rutas públicas: login, registro, página de inicio.
| Rutas protegidas: dashboard y áreas internas (middleware auth).
| CSRF aplicado automáticamente al grupo web.
|
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API interna: validar QR y contador
    Route::get('/api/consumo/entregados-hoy', [ConsumoValidarQrController::class, 'entregadosHoy'])
        ->middleware('role:casino,operativo')
        ->name('api.consumo.entregados-hoy');
    Route::post('/api/consumo/validar-qr', [ConsumoValidarQrController::class, 'validarQr'])
        ->middleware('role:casino,operativo')
        ->name('api.consumo.validar-qr');

    // Módulo casino: escaneo QR y panel
    Route::get('/casino/escaneo-qr', [CasinoEscaneoController::class, 'index'])
        ->middleware('role:casino,operativo')
        ->name('casino.escaneo-qr');
    Route::get('/casino/panel', [CasinoPanelController::class, 'index'])
        ->middleware('role:casino,operativo,administrador')
        ->name('casino.panel');
    Route::get('/casino/panel/datos', [CasinoPanelController::class, 'datos'])
        ->middleware('role:casino,operativo,administrador')
        ->name('casino.panel.datos');

    // Cuenta de cobro: generar PDF, descargar, enviar a contabilidad
    Route::get('/casino/cuenta-cobro', [CuentaCobroController::class, 'index'])
        ->middleware('role:casino,operativo,administrador')
        ->name('casino.cuenta-cobro.index');
    Route::post('/casino/cuenta-cobro/generar', [CuentaCobroController::class, 'generar'])
        ->middleware('role:casino,operativo,administrador')
        ->name('casino.cuenta-cobro.generar');
    Route::get('/casino/cuenta-cobro/{id}/descargar', [CuentaCobroController::class, 'descargar'])
        ->middleware('role:casino,operativo,administrador')
        ->name('casino.cuenta-cobro.descargar');
    Route::post('/casino/cuenta-cobro/{id}/enviar', [CuentaCobroController::class, 'enviarCorreo'])
        ->middleware('role:casino,operativo,administrador')
        ->name('casino.cuenta-cobro.enviar');

    // Gestión Humana: reportes por fechas, consumo por empresa, exportar PDF y Excel
    Route::get('/gestion-humana', [GestionHumanaController::class, 'index'])
        ->middleware('role:gestionhumana,administrador')
        ->name('gestion-humana.index');
    Route::get('/gestion-humana/exportar-pdf', [GestionHumanaController::class, 'exportarPdf'])
        ->middleware('role:gestionhumana,administrador')
        ->name('gestion-humana.exportar-pdf');
    Route::get('/gestion-humana/exportar-excel', [GestionHumanaController::class, 'exportarExcel'])
        ->middleware('role:gestionhumana,administrador')
        ->name('gestion-humana.exportar-excel');

    // Empleados: solicitar consumo (casino). Si casino tiene tipo_casino=domicilio, crea pedido a domicilio.
    Route::middleware('role:empleado')->group(function () {
        Route::get('/solicitar-consumo', [SolicitarConsumoController::class, 'create'])->name('solicitar-consumo.create');
        Route::post('/solicitar-consumo', [SolicitarConsumoController::class, 'store'])->name('solicitar-consumo.store');
        Route::get('/solicitar-consumo/mostrar-qr/{consumo}', [SolicitarConsumoController::class, 'mostrarQr'])->name('solicitar-consumo.mostrar-qr');
    });

    // Módulo operativo: pedidos a domicilio pendientes y marcar ENTREGADO
    Route::middleware('role:operativo')->prefix('operativo')->name('operativo.')->group(function () {
        Route::get('/pedidos', [OperativoPedidosController::class, 'index'])->name('pedidos.index');
        Route::post('/pedidos/{consumo}/marcar-entregado', [OperativoPedidosController::class, 'marcarEntregado'])->name('pedidos.marcar-entregado');
    });

    // Módulo administrador
    Route::middleware('role:administrador')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::resource('empresas', EmpresaController::class)->parameters(['empresa' => 'empresa']);
        Route::resource('casinos', AdminCasinoController::class)->parameters(['casino' => 'casino']);
        Route::resource('horarios', HorarioController::class)->parameters(['horario' => 'horario']);
        Route::resource('tipos-usuario', TipoUsuarioController::class)->parameters(['tipo_usuario' => 'tipoUsuario']);
        Route::resource('visitantes', VisitanteController::class)->parameters(['visitante' => 'visitante']);
        Route::resource('roles', RoleController::class)->parameters(['role' => 'role']);
        Route::resource('usuarios', AdminUsuarioController::class)->parameters(['usuario' => 'usuario']);
        Route::resource('precios', PrecioController::class)->parameters(['precio' => 'precio']);
        Route::get('consumos-manuales', [ConsumoManualController::class, 'index'])->name('consumos-manuales.index');
        Route::get('consumos-manuales/crear', [ConsumoManualController::class, 'create'])->name('consumos-manuales.create');
        Route::post('consumos-manuales', [ConsumoManualController::class, 'store'])->name('consumos-manuales.store');
    });
});
