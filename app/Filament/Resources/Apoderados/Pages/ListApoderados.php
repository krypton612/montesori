<?php

namespace App\Filament\Resources\Apoderados\Pages;

use App\Filament\Resources\Apoderados\ApoderadoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApoderados extends ListRecords
{
    protected static string $resource = ApoderadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
