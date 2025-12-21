<?php

namespace App\Filament\Resources\TipoEvaluacions\Pages;

use App\Filament\Resources\TipoEvaluacions\TipoEvaluacionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoEvaluacions extends ListRecords
{
    protected static string $resource = TipoEvaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
