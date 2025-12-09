<?php

namespace App\Filament\Resources\Inscripcions\Pages;

use App\Filament\Resources\Inscripcions\InscripcionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInscripcion extends ViewRecord
{
    protected static string $resource = InscripcionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
