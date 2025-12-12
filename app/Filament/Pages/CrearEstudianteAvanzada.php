<?php

namespace App\Filament\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class CrearEstudianteAvanzada extends Page implements HasForms
{
    protected string $view = 'filament.pages.crear-estudiante-avanzada';

    protected static string|null|\UnitEnum $navigationGroup = 'Inscripcion Estudiantil';

    protected static ?string $navigationLabel = 'Registro Estudiantil';

    protected static ?string $title = 'Formulario de Registro Avanzado';
}
