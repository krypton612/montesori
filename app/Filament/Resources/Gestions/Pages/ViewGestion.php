<?php

namespace App\Filament\Resources\Gestions\Pages;

use App\Filament\Resources\Gestions\GestionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGestion extends ViewRecord
{
    protected static string $resource = GestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
