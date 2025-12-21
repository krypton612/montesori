<?php

namespace App\Filament\Resources\Estados\Pages;

use App\Filament\Resources\Estados\EstadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEstados extends ListRecords
{
    protected static string $resource = EstadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
