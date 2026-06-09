<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteAuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\PagoSolicitudController;
use App\Http\Controllers\SolicitudManualController;

/*
|--------------------------------------------------------------------------
| API Routes — ODI3D tienda3d
|--------------------------------------------------------------------------
*/

// Estado de módulos (público, sin auth)
Route::get('/modulos/{nombre}/estado', [ModuloController::class, 'estado']);

// Autenticación de clientes
Route::prefix('auth')->group(function () {
    Route::post('/registro', [ClienteAuthController::class, 'registro'])
        ->middleware('throttle:10,1');
    Route::post('/login', [ClienteAuthController::class, 'login'])
        ->middleware('throttle:5,1');
    Route::post('/logout', [ClienteAuthController::class, 'logout'])
        ->middleware('auth:sanctum');
    Route::get('/me', [ClienteAuthController::class, 'me'])
        ->middleware('auth:sanctum');
});

// Catálogo — público
Route::middleware('modulo:catalogo')->group(function () {
    Route::get('/catalogo/productos', [ProductoController::class, 'index']);
    Route::get('/catalogo/productos/{id}', [ProductoController::class, 'show']);
    Route::get('/catalogo/productos/{id}/reviews', [ReviewController::class, 'index']);
});

// Reviews — requiere auth de cliente
Route::middleware(['modulo:catalogo', 'auth:sanctum'])->group(function () {
    Route::post('/catalogo/productos/{id}/reviews', [ReviewController::class, 'store']);
});

// Solicitudes — requiere auth + módulo activo
Route::middleware(['modulo:solicitudes', 'auth:sanctum'])->group(function () {
    Route::post('/solicitudes', [SolicitudController::class, 'store']);
    Route::get('/solicitudes', [SolicitudController::class, 'misSolicitudes']);
    Route::get('/solicitudes/{id}', [SolicitudController::class, 'show']);
    Route::put('/solicitudes/{id}', [SolicitudController::class, 'update']);
    Route::get('/solicitudes/{id}/estado', [SolicitudController::class, 'estado']);
    Route::get('/solicitudes/{solicitudId}/archivos/{archivoId}', [SolicitudController::class, 'descargarArchivo']);
    Route::delete('/solicitudes/{id}/archivos/{archivoId}', [SolicitudController::class, 'eliminarArchivo']);
});

// ─────────────────────────────────────────────────────────────────────────────
// Rutas INTERNAS — usadas únicamente por gestion3d vía proxy server-to-server.
// Sin Sanctum (mismo modelo de confianza que las escrituras directas a BD de
// gestion3d), protegidas solo por modulo:solicitudes para verificar que el
// módulo está activo.
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('interno')->middleware('modulo:solicitudes')->group(function () {
    // Pagos
    Route::get('/solicitudes/{id}/pagos',
        [PagoSolicitudController::class, 'index']);
    Route::post('/solicitudes/{id}/pagos',
        [PagoSolicitudController::class, 'store']);
    Route::get('/solicitudes/{solicitudId}/pagos/{pagoId}/comprobante',
        [PagoSolicitudController::class, 'descargarComprobante']);

    // Solicitudes manuales (creación desde el panel de gestion3d)
    Route::post('/solicitudes',
        [SolicitudManualController::class, 'store']);
    Route::post('/solicitudes/{id}/items',
        [SolicitudManualController::class, 'storeItems']);
    Route::get('/solicitudes/{id}/items',
        [SolicitudManualController::class, 'indexItems']);
    Route::post('/solicitudes/{id}/vincular-cliente',
        [SolicitudManualController::class, 'vincularCliente']);
});

// Chat — requiere auth + módulo solicitudes activo
Route::middleware(['modulo:solicitudes', 'auth:sanctum'])->group(function () {
    Route::get('/solicitudes/{solicitudId}/mensajes', [ChatController::class, 'index']);
    Route::post('/solicitudes/{solicitudId}/mensajes', [ChatController::class, 'store']);
});
