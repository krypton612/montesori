<?php

namespace App\Filament\Resources\Gestions\Pages;

use App\Filament\Resources\Gestions\GestionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGestion extends EditRecord
{
    protected static string $resource = GestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
