<?php

namespace App\Filament\Resources\TipoEvaluacions\Pages;

use App\Filament\Resources\TipoEvaluacions\TipoEvaluacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoEvaluacion extends EditRecord
{
    protected static string $resource = TipoEvaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
