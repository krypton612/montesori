<?php

namespace App\Filament\Resources\Materias\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MateriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->placeholder("Matemáticas Avanzadas")
                    ->prefix("MAT-")
                    ->unique(ignoreRecord: true)
                    ->prefixIcon(Heroicon::OutlinedBookOpen)
                    ->required(),
                TextInput::make('nivel')
                    ->prefixIcon(Heroicon::OutlinedArrowDown)
                    ->numeric(),
                TextInput::make('horas_semanales')
                    ->prefixIcon(Heroicon::OutlinedClock)
                    ->numeric(),
                Textarea::make('descripcion')->minLength(100),
                Toggle::make('habilitado')
                    ->required(),
            ]);
    }
}
