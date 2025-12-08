<?php

namespace App\Filament\Resources\Estudiantes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EstudianteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('persona_id')
                    ->required()
                    ->numeric(),
                TextInput::make('codigo_saga'),
                TextInput::make('estado_academico'),
                Toggle::make('tiene_discapacidad')
                    ->required(),
                Textarea::make('observaciones')
                    ->columnSpanFull(),
                TextInput::make('foto_url')
                    ->url(),
            ]);
    }
}
