<?php

namespace App\Filament\Resources\TipoDiscapacidads\Pages;

use App\Filament\Resources\TipoDiscapacidads\TipoDiscapacidadResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTipoDiscapacidad extends ViewRecord
{
    protected static string $resource = TipoDiscapacidadResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return TipoDiscapacidadInfolist::configure($infolist);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
