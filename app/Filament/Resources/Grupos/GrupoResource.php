<?php

namespace App\Filament\Resources\Grupos;

use App\Filament\Resources\Grupos\Pages\CreateGrupo;
use App\Filament\Resources\Grupos\Pages\EditGrupo;
use App\Filament\Resources\Grupos\Pages\ListGrupos;
use App\Filament\Resources\Grupos\Pages\ViewGrupo;
use App\Filament\Resources\Grupos\Schemas\GrupoForm;
use App\Filament\Resources\Grupos\Schemas\GrupoInfolist;
use App\Filament\Resources\Grupos\Tables\GruposTable;
use App\Models\Grupo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GrupoResource extends Resource
{
    protected static ?string $model = Grupo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $recordTitleAttribute = 'codigo';

    protected static ?string $navigationLabel = 'Grupos Escolares';

    public static function form(Schema $schema): Schema
    {
        return GrupoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GrupoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GruposTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGrupos::route('/'),
            'create' => CreateGrupo::route('/create'),
            'view' => ViewGrupo::route('/{record}'),
            'edit' => EditGrupo::route('/{record}/edit'),
        ];
    }
}
