<?php

namespace App\Filament\Inscripcion\Resources\Inscripcions\Pages;

use App\Filament\Inscripcion\Resources\Inscripcions\InscripcionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInscripcion extends EditRecord
{
    protected static string $resource = InscripcionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
