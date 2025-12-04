<?php

namespace App\Filament\Resources\TipoEvaluacions;

use App\Filament\Resources\TipoEvaluacions\Pages\CreateTipoEvaluacion;
use App\Filament\Resources\TipoEvaluacions\Pages\EditTipoEvaluacion;
use App\Filament\Resources\TipoEvaluacions\Pages\ListTipoEvaluacions;
use App\Filament\Resources\TipoEvaluacions\Schemas\TipoEvaluacionForm;
use App\Filament\Resources\TipoEvaluacions\Tables\TipoEvaluacionsTable;
use App\Models\TipoEvaluacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TipoEvaluacionResource extends Resource
{
    protected static ?string $model = TipoEvaluacion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return TipoEvaluacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoEvaluacionsTable::configure($table);
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
            'index' => ListTipoEvaluacions::route('/'),
            'create' => CreateTipoEvaluacion::route('/create'),
            'edit' => EditTipoEvaluacion::route('/{record}/edit'),
        ];
    }
}
