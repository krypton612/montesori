<?php

namespace App\Filament\Resources\Grupos\RelationManagers;

use App\Filament\Resources\Inscripcions\InscripcionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class InscritosRelationManager extends RelationManager
{
    protected static string $relationship = 'inscritos';

    protected static ?string $relatedResource = InscripcionResource::class;

    protected static ?string $label = 'Inscrito';

    protected static ?string $pluralLabel = 'Inscritos';

    protected static ?string $title = 'Inscritos al grupo de cursos';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
