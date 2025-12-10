<?php

namespace App\Filament\Resources\TipoDiscapacidads;

use App\Filament\Resources\TipoDiscapacidads\RelationManagers\DiscapacidadesRelationManager;
use App\Filament\Resources\TipoDiscapacidads\Pages\CreateTipoDiscapacidad;
use App\Filament\Resources\TipoDiscapacidads\Pages\EditTipoDiscapacidad;
use App\Filament\Resources\TipoDiscapacidads\Pages\ListTipoDiscapacidads;
use App\Filament\Resources\TipoDiscapacidads\Pages\ViewTipoDiscapacidad;
use App\Filament\Resources\TipoDiscapacidads\Schemas\TipoDiscapacidadForm;
use App\Filament\Resources\TipoDiscapacidads\Schemas\TipoDiscapacidadInfolist;
use App\Filament\Resources\TipoDiscapacidads\Tables\TipoDiscapacidadsTable;
use App\Models\TipoDiscapacidad;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TipoDiscapacidadResource extends Resource
{
    protected static ?string $model = TipoDiscapacidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Eye;
    
    protected static string|UnitEnum|null $navigationGroup = 'Parametros';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return TipoDiscapacidadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TipoDiscapacidadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoDiscapacidadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DiscapacidadesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTipoDiscapacidads::route('/'),
            'create' => CreateTipoDiscapacidad::route('/create'),
            'view' => ViewTipoDiscapacidad::route('/{record}'),
            'edit' => EditTipoDiscapacidad::route('/{record}/edit'),
        ];
    }
}
