<?php

namespace App\Filament\Resources\Apoderados\Pages;

use App\Filament\Resources\Apoderados\ApoderadoResource;
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
