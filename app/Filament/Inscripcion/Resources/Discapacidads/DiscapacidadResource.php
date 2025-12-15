<?php

namespace App\Filament\Resources\Discapacidads;

use App\Filament\Resources\Discapacidads\Pages;
use App\Filament\Resources\Discapacidads\Schemas\DiscapacidadForm;
use App\Filament\Resources\Discapacidads\Schemas\DiscapacidadInfolist;
use App\Filament\Resources\Discapacidads\Tables\DiscapacidadsTable;
use App\Models\Discapacidad;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DiscapacidadResource extends Resource
{
    protected static ?string $model = Discapacidad::class;

    // Grupo de menú SIN acentos, alineado con el resto
    protected static string|UnitEnum|null $navigationGroup = 'Gestion Personas';
    protected static null|string $navigationLabel = 'Discapacidades';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return DiscapacidadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DiscapacidadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiscapacidadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDiscapacidads::route('/'),
            'create' => Pages\CreateDiscapacidad::route('/create'),
            'view'   => Pages\ViewDiscapacidad::route('/{record}'),
            'edit'   => Pages\EditDiscapacidad::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}
