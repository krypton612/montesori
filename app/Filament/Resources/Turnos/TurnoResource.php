<?php

namespace App\Filament\Resources\Turnos;

use App\Filament\Resources\Turnos\Pages\CreateTurno;
use App\Filament\Resources\Turnos\Pages\EditTurno;
use App\Filament\Resources\Turnos\Pages\ListTurnos;
use App\Filament\Resources\Turnos\Pages\ViewTurno;
use App\Filament\Resources\Turnos\Schemas\TurnoForm;
use App\Filament\Resources\Turnos\Schemas\TurnoInfolist;
use App\Filament\Resources\Turnos\Tables\TurnosTable;
use App\Models\Turno;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TurnoResource extends Resource
{
    protected static ?string $model = Turno::class;

    protected static string |null|\UnitEnum $navigationGroup = 'Gestion Academica';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return TurnoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TurnoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TurnosTable::configure($table);
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
            'index' => ListTurnos::route('/'),
            'create' => CreateTurno::route('/create'),
            'view' => ViewTurno::route('/{record}'),
            'edit' => EditTurno::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
