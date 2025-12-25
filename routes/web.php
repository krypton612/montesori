<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentsController;

Route::get('/', function () {
    return view('welcome');
});




Route::get('inscripcion/hoja-datos/preview/{id}', [DocumentsController::class, 'previewInscripcionHojaDeDatos'])
    ->name('documentos.inscripcion_hoja_datos.preview');
Route::get('inscripcion/hoja-datos/descargar/{id}', [DocumentsController::class, 'descargarInscripcionHojaDeDatos'])
    ->name('documentos.inscripcion_hoja_datos.descargar');

Route::get('/documentos/descargar/compromiso/{id}', [DocumentsController::class, 'descargarCompromisoEstudiantesNuevos'])
    ->name('documentos.descargar.compromiso');

Route::get('/documentos/preview/compromiso/{id}', [DocumentsController::class, 'previewCompromisoEstudiantesNuevos'])
    ->name('documentos.preview.compromiso');

Route::get('kardex-apoderados/{id}/download', [DocumentsController::class, 'descargarKardexApoderados'])
    ->name('documents.kardex_apoderados.download');

Route::get('kardex-apoderados/{id}/preview', [DocumentsController::class, 'previewKardexApoderados'])
    ->name('documents.kardex_apoderados.preview');

Route::get('kardex-estudiante/{id}/download', [DocumentsController::class, 'descargarKardexEstudiante'])
    ->name('documents.kardex_estudiante.download');

Route::get('kardex-estudiante/{id}/preview', [DocumentsController::class, 'previewKardexEstudiante'])
    ->name('documents.kardex_estudiante.preview');
