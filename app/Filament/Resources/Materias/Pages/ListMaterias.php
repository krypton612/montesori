<?php

namespace App\Filament\Resources\Materias\Pages;

use App\Filament\Resources\Materias\MateriaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterias extends ListRecords
{
    protected static string $resource = MateriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
