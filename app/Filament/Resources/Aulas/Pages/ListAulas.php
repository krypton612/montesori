<?php

namespace App\Filament\Resources\Aulas\Pages;

use App\Filament\Resources\Aulas\AulaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAulas extends ListRecords
{
    protected static string $resource = AulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
