<?php

namespace App\Filament\Resources\Estados;

use App\Filament\Resources\Estados\Pages\CreateEstado;
use App\Filament\Resources\Estados\Pages\EditEstado;
use App\Filament\Resources\Estados\Pages\ListEstados;
use App\Filament\Resources\Estados\Schemas\EstadoForm;
use App\Filament\Resources\Estados\Tables\EstadosTable;
use App\Models\Estado;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EstadoResource extends Resource
{
    protected static ?string $model = Estado::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|UnitEnum|null $navigationGroup = 'Parametros';


    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return EstadoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EstadosTable::configure($table);
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
            'index' => ListEstados::route('/'),
            'create' => CreateEstado::route('/create'),
            'edit' => EditEstado::route('/{record}/edit'),
        ];
    }
}
