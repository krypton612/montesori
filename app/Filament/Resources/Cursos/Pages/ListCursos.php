<?php

namespace App\Filament\Resources\Cursos\Pages;

use App\Filament\Resources\Cursos\CursoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCursos extends ListRecords
{
    protected static string $resource = CursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
