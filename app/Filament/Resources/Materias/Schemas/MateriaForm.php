<?php

namespace App\Filament\Resources\Materias\Schemas;

use Filament\Forms\Components\Select;
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
                    ->prefix('📚')
                    ->prefixIcon(Heroicon::OutlinedBookOpen)
                    ->required()
                    ->live()
                    ,
                TextInput::make('nivel')
                    ->prefixIcon(Heroicon::OutlinedArrowDown)
                    ->numeric(),
                TextInput::make('horas_semanales')
                    ->prefixIcon(Heroicon::OutlinedClock)
                    ->numeric(),
                Textarea::make('descripcion')->minLength(10),
                Select::make('grado')
                    ->live()
                    ->options([
                        'PEDAGOGIA' => 'Pedagogía',
                        'INICIAL' => 'Inicial',
                        'PRIMARIA' => 'Primaria',
                        'SECUNDARIA' => 'Secundaria',
                        'CURSO_INDIVIDUAL' => 'Curso Individual',
                    ])
                    ->required(),
                Toggle::make('habilitado')
                    ->required(),
            ]);
    }
}
