<?php

namespace App\Filament\Resources\Profesors;

use App\Filament\Resources\Aulas\Pages\ViewProfesor;
use App\Filament\Resources\Aulas\Schemas\ProfesorInfolist;
use App\Filament\Resources\Profesors\Pages\CreateProfesor;
use App\Filament\Resources\Profesors\Pages\EditProfesor;
use App\Filament\Resources\Profesors\Pages\ListProfesors;
use App\Filament\Resources\Profesors\Schemas\ProfesorForm;
use App\Filament\Resources\Profesors\Tables\ProfesorsTable;
use App\Models\Profesor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ProfesorResource extends Resource
{
    protected static ?string $model = Profesor::class;

    protected static string|UnitEnum|null $navigationGroup = 'Gestion Personas';
    protected static null|string $navigationLabel = 'Profesores';

    protected static ?string $recordTitleAttribute = 'nombre';

    

    public static function form(Schema $schema): Schema
    {
        return ProfesorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProfesorInfolist::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return ProfesorsTable::configure($table);
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
            'index' => ListProfesors::route('/'),
            'create' => CreateProfesor::route('/create'),
            'edit' => EditProfesor::route('/{record}/edit'),
            'view' => ViewProfesor::route('/{record}'),

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
