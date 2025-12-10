<?php

namespace App\Filament\Resources\Inscripcions;

use App\Filament\Resources\Inscripcions\Pages\CreateInscripcion;
use App\Filament\Resources\Inscripcions\Pages\EditInscripcion;
use App\Filament\Resources\Inscripcions\Pages\ListInscripcions;
use App\Filament\Resources\Inscripcions\Pages\ViewInscripcion;
use App\Filament\Resources\Inscripcions\Schemas\InscripcionForm;
use App\Filament\Resources\Inscripcions\Schemas\InscripcionInfolist;
use App\Filament\Resources\Inscripcions\Tables\InscripcionsTable;
use App\Models\Inscripcion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class InscripcionResource extends Resource
{
    protected static ?string $model = Inscripcion::class;

    protected static string | UnitEnum | null $navigationGroup = 'Inscripcion Estudiantil';

    protected static ?string $recordTitleAttribute = 'codigo_inscripcion';

    protected static ?string $navigationLabel = 'Inscripciones';

    public static function form(Schema $schema): Schema
    {
        return InscripcionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InscripcionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InscripcionsTable::configure($table);
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
            'index' => ListInscripcions::route('/'),
            'create' => CreateInscripcion::route('/create'),
            'view' => ViewInscripcion::route('/{record}'),
            'edit' => EditInscripcion::route('/{record}/edit'),
        ];
    }
}
