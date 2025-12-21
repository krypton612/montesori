<?php

namespace App\Filament\Resources\Aulas\Pages;

use App\Filament\Resources\Aulas\AulaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAula extends ViewRecord
{
    protected static string $resource = AulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
