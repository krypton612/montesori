<?php

namespace App\Filament\Resources\Profesors\Pages;

use App\Filament\Resources\Profesors\ProfesorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;


class ViewProfesor extends ViewRecord
{
    protected static string $resource = ProfesorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
