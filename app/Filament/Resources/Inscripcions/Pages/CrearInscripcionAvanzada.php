<?php

namespace App\Filament\Resources\Inscripcions\Pages;

use App\Filament\Resources\Inscripcions\InscripcionResource;
use Filament\Resources\Pages\Page;

class CrearInscripcionAvanzada extends Page
{
    protected static string $resource = InscripcionResource::class;

    protected string $view = 'filament.resources.inscripcions.pages.crear-inscripcion-avanzada';
}
