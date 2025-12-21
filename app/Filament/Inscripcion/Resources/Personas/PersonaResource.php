<?php

namespace App\Filament\Inscripcion\Resources\Personas;

use App\Filament\Inscripcion\Resources\Personas\Pages\CreatePersona;
use App\Filament\Inscripcion\Resources\Personas\Pages\EditPersona;
use App\Filament\Inscripcion\Resources\Personas\Pages\ListPersonas;
use App\Filament\Inscripcion\Resources\Personas\Schemas\PersonaForm;
use App\Filament\Inscripcion\Resources\Personas\Tables\PersonasTable;
use App\Models\Persona;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PersonaResource extends Resource
{
    protected static ?string $model = Persona::class;

    protected static string|UnitEnum|null $navigationGroup = 'Gestion Personas';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return PersonaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PersonasTable::configure($table);
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
            'index' => ListPersonas::route('/'),
            'create' => CreatePersona::route('/create'),
            'edit' => EditPersona::route('/{record}/edit'),
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
