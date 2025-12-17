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
