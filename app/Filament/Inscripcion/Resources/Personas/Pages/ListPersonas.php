<?php

namespace App\Filament\Inscripcion\Resources\Personas\Pages;

use App\Filament\Inscripcion\Resources\Personas\PersonaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPersonas extends ListRecords
{
    protected static string $resource = PersonaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
