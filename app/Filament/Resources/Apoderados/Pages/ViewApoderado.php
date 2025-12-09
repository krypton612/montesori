<?php

namespace App\Filament\Resources\Apoderados\Pages;

use App\Filament\Resources\Apoderados\ApoderadoResource;
use App\Filament\Resources\Apoderados\Schemas\ApoderadoInfolist;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewApoderado extends ViewRecord
{
    protected static string $resource = ApoderadoResource::class;

    public function infolist(Schema $schema): Schema
    {
        return ApoderadoInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
