<?php

namespace App\Filament\Profesor\Resources\Cursos;

use App\Filament\Profesor\Resources\Cursos\Pages\CreateCurso;
use App\Filament\Profesor\Resources\Cursos\Pages\EditCurso;
use App\Filament\Profesor\Resources\Cursos\Pages\ListCursos;
use App\Filament\Profesor\Resources\Cursos\Pages\ViewCurso;
use App\Filament\Profesor\Resources\Cursos\RelationManagers\InscripcionesRelationManager;
use App\Filament\Profesor\Resources\Cursos\Schemas\CursoForm;
use App\Filament\Profesor\Resources\Cursos\Schemas\CursoInfolist;
use App\Filament\Profesor\Resources\Cursos\Tables\CursosTable;
use App\Filament\Profesor\Resources\Cursos\RelationManagers\EvaluacionesRelationManager;
use App\Models\Curso;
use App\Models\Evaluacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::AcademicCap;

    protected static ?string $recordTitleAttribute = 'Curso';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión Académica';

    protected static string|null $navigationLabel = 'Mis Cursos';

    public static function form(Schema $schema): Schema
    {
        return CursoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CursoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CursosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            InscripcionesRelationManager::class,
            EvaluacionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCursos::route('/'),
            'create' => CreateCurso::route('/create'),
            'view' => ViewCurso::route('/{record}'),
            'edit' => EditCurso::route('/{record}/edit'),
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
