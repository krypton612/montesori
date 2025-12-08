<?php

namespace App\Filament\Resources\Estudiantes;

use App\Filament\Resources\Estudiantes\Pages\CreateEstudiante;
use App\Filament\Resources\Estudiantes\Pages\EditEstudiante;
use App\Filament\Resources\Estudiantes\Pages\ListEstudiantes;
use App\Filament\Resources\Estudiantes\Pages\ViewEstudiante;
use App\Filament\Resources\Estudiantes\Schemas\EstudianteForm;
use App\Filament\Resources\Estudiantes\Schemas\EstudianteInfolist;
use App\Filament\Resources\Estudiantes\Tables\EstudiantesTable;
use App\Models\Estudiante;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class EstudianteResource extends Resource
{
    protected static ?string $model = Estudiante::class;

    protected static string | UnitEnum | null $navigationGroup = 'Gestion Personas';

    protected static ?string $recordTitleAttribute = 'codigo_saga';

    public static function form(Schema $schema): Schema
    {
        return EstudianteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EstudianteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EstudiantesTable::configure($table);
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
            'index' => ListEstudiantes::route('/'),
            'create' => CreateEstudiante::route('/create'),
            'view' => ViewEstudiante::route('/{record}'),
            'edit' => EditEstudiante::route('/{record}/edit'),
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
