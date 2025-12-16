<?php

namespace App\Filament\Inscripcion\Resources\Apoderados;

use App\Filament\Inscripcion\Resources\Apoderados\RelationManagers;
use App\Filament\Inscripcion\Resources\Apoderados\Pages\CreateApoderado;
use App\Filament\Inscripcion\Resources\Apoderados\Pages\EditApoderado;
use App\Filament\Inscripcion\Resources\Apoderados\Pages\ListApoderados;
use App\Filament\Inscripcion\Resources\Apoderados\Pages\ViewApoderado;
use App\Filament\Inscripcion\Resources\Apoderados\Schemas\ApoderadoForm;
use App\Filament\Inscripcion\Resources\Apoderados\Schemas\ApoderadoInfolist;
use App\Filament\Inscripcion\Resources\Apoderados\Tables\ApoderadosTable;
use App\Models\Apoderado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ApoderadoResource extends Resource
{
    protected static ?string $model = Apoderado::class;

    // Ajusta el grupo si quieres moverlo luego
    protected static string|UnitEnum|null $navigationGroup = 'Gestion Personas';

    // Si tienes accessor nombre_completo en Persona, mantenlo. Si no, puedes cambiarlo.
    protected static ?string $recordTitleAttribute = 'persona.nombre_completo';

    public static function form(Schema $schema): Schema
    {
        return ApoderadoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApoderadoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApoderadosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EstudiantesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListApoderados::route('/'),
            'create' => CreateApoderado::route('/create'),
            'view'   => ViewApoderado::route('/{record}'),
            'edit'   => EditApoderado::route('/{record}/edit'),
        ];
    }

    // Badge con la cantidad de apoderados en el menú
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}
