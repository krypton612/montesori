<?php

namespace App\Filament\Inscripcion\Resources\TipoDiscapacidads\Pages;

use App\Filament\Inscripcion\Resources\TipoDiscapacidads\TipoDiscapacidadResource;
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
