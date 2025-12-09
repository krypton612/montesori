<?php

namespace App\Filament\Resources\TipoDiscapacidads\Pages;

use App\Filament\Resources\TipoDiscapacidads\TipoDiscapacidadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTipoDiscapacidads extends ManageRecords
{
    protected static string $resource = TipoDiscapacidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
