<?php

namespace App\Filament\Resources\Materias;

use App\Filament\Resources\Materias\Pages\CreateMateria;
use App\Filament\Resources\Materias\Pages\EditMateria;
use App\Filament\Resources\Materias\Pages\ListMaterias;
use App\Filament\Resources\Materias\Pages\ViewMateria;
use App\Filament\Resources\Materias\RelationManagers\MallasRelationManager;
use App\Filament\Resources\Materias\Schemas\MateriaForm;
use App\Filament\Resources\Materias\Schemas\MateriaInfolist;
use App\Filament\Resources\Materias\Tables\MateriasTable;
use App\Models\Materia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MateriaResource extends Resource
{
    protected static ?string $model = Materia::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Gestion Academica';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return MateriaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MateriaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MateriasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MallasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaterias::route('/'),
            'create' => CreateMateria::route('/create'),
            'view' => ViewMateria::route('/{record}'),
            'edit' => EditMateria::route('/{record}/edit'),
        ];
    }
}
