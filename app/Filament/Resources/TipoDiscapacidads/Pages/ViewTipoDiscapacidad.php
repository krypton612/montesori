<?php

namespace App\Filament\Resources\TipoDiscapacidads\Pages;

use App\Filament\Resources\TipoDiscapacidads\TipoDiscapacidadResource;
use App\Filament\Resources\TipoDiscapacidads\Infolists\TipoDiscapacidadInfolist;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewTipoDiscapacidad extends ViewRecord
{
    protected static string $resource = TipoDiscapacidadResource::class;

    public function infolist(Schema $schema): Schema
    {
        return TipoDiscapacidadInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
