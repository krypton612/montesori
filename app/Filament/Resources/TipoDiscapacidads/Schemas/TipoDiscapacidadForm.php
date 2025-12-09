<?php

namespace App\Filament\Resources\TipoDiscapacidads\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TipoDiscapacidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Datos del tipo de discapacidad')
                    ->columns(1)
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        TextInput::make('descripcion')
                            ->label('Descripción')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
