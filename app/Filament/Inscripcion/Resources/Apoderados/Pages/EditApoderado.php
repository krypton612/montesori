<?php

namespace App\Filament\Inscripcion\Resources\Apoderados\Pages;

use App\Filament\Inscripcion\Resources\Apoderados\ApoderadoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditApoderado extends EditRecord
{
    protected static string $resource = ApoderadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
