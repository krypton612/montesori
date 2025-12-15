<?php

namespace App\Filament\Resources\Discapacidads\Pages;

use App\Filament\Resources\Discapacidads\DiscapacidadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiscapacidads extends ListRecords
{
    protected static string $resource = DiscapacidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
