<?php

namespace App\Filament\Resources\TipoDiscapacidads\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TipoDiscapacidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->helperText('Nombre corto del tipo de discapacidad')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->autofocus(),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->placeholder('Describe brevemente el tipo de discapacidad...')
                    ->rows(3)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
