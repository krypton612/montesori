<?php

namespace App\Filament\Inscripcion\Resources\Apoderados\Pages;

use App\Filament\Inscripcion\Resources\Apoderados\ApoderadoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewApoderado extends ViewRecord
{
    protected static string $resource = ApoderadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
