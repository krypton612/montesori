<?php

namespace App\Filament\Resources\Discapacidads\Pages;

use App\Filament\Resources\Discapacidads\DiscapacidadResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDiscapacidad extends ViewRecord
{
    protected static string $resource = DiscapacidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
