<?php

use App\Events\MensajePrueba;
use App\Http\Controllers\Admin\LugarController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\Admin\ReporteSalaController;

use Illuminate\Support\Facades\Route;

Route::get('/emitir-prueba', function () {
    event(new MensajePrueba());

    return response()->json([
        'success' => true,
        'mensaje' => 'Evento enviado a Reverb',
    ]);
});
Route::view('/websocket-test', 'websocket');
Route::view('/', 'impostor.inicio');

Route::get('/perfil', [JugadorController::class, 'perfil'])->name('perfil');
route::get('/sala', [SalaController::class, 'VisualizarSala'])->name('sala');
Route::post('/sala/entrar', [SalaController::class, 'entrar'])->name('sala.entrar');
Route::post('/perfil', [JugadorController::class, 'guardar'])->name('perfil.guardar');
Route::get('/rol', [SalaController::class, 'mostrarRol'])->name('rol');
Route::get('/espera', [SalaController::class, 'espera'])->name('sala.espera');
Route::post('/rol/iniciar', [SalaController::class, 'iniciar'])->name('rol.iniciar');
Route::post('/sala/iniciar', [SalaController::class, 'iniciar'])
    ->name('sala.iniciar');
Route::get('/tareas', [SalaController::class, 'tareas'])
    ->name('tareas');
Route::post('/tareas/completar', [SalaController::class, 'completarMision'])
    ->name('tareas.completar');

Route::group(['prefix' => 'admin'], function () {

    Voyager::routes();

    Route::get('/lugares', [LugarController::class, 'index'])
        ->name('voyager.lugares.index');
    Route::get('/reportes', [ReporteSalaController::class, 'index'])
        ->name('voyager.reportes.index');
    Route::post('/lugares/guardar', [LugarController::class, 'guardar'])
        ->name('voyager.lugares.guardar');
    Route::get('/reporte-salas/{id}', [ReporteSalaController::class, 'tareasSala'])
    ->name('voyager.reporte-salas.detalle');

});

Route::view('/resultados', 'impostor.inicio')->name('resultados');

Route::post('/jugador/he-muerto', [SalaController::class, 'heMuerto'])
    ->name('jugador.heMuerto');
Route::post('/reunion/emergencia', [SalaController::class, 'encontrarMuerto'])
    ->name('reunion.emergencia');
Route::get('/reunion/{id}', [SalaController::class, 'verReunion'])
    ->name('reunion.ver');
Route::post('/reunion/{id}/votar', [SalaController::class, 'votar'])
    ->name('reunion.votar');

Route::post('/reunion/{id}/cerrar-votacion', [SalaController::class, 'cerrarVotacion'])
    ->name('reunion.cerrarVotacion');


Route::view('/prueba-nfc', 'prueba-nfc')->name('prueba.nfc');