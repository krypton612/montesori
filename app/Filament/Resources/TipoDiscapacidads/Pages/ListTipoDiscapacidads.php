<?php

namespace App\Filament\Resources\TipoDiscapacidads\Pages;

use App\Filament\Resources\TipoDiscapacidads\TipoDiscapacidadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoDiscapacidads extends ListRecords
{
    protected static string $resource = TipoDiscapacidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
