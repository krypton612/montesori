<?php

namespace App\Filament\Inscripcion\Resources\TipoDiscapacidads\Pages;

use App\Filament\Inscripcion\Resources\TipoDiscapacidads\TipoDiscapacidadResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTipoDiscapacidad extends ViewRecord
{
    protected static string $resource = TipoDiscapacidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
