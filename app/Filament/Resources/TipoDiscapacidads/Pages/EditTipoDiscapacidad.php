<?php

namespace App\Filament\Resources\TipoDiscapacidads\Pages;

use App\Filament\Resources\TipoDiscapacidads\TipoDiscapacidadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoDiscapacidad extends EditRecord
{
    protected static string $resource = TipoDiscapacidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
