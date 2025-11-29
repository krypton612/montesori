<?php

namespace App\Filament\Resources\Estados\Pages;

use App\Filament\Resources\Estados\EstadoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEstado extends EditRecord
{
    protected static string $resource = EstadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
