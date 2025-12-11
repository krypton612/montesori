<?php

namespace App\Filament\Resources\Discapacidads\Pages;

use App\Filament\Resources\Discapacidads\DiscapacidadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDiscapacidad extends EditRecord
{
    protected static string $resource = DiscapacidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
