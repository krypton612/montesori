<?php

namespace App\Filament\Resources\Inscripcions\Pages;

use App\Filament\Resources\Inscripcions\InscripcionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInscripcions extends ListRecords
{
    protected static string $resource = InscripcionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
