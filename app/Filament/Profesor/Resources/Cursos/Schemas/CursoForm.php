<?php

namespace App\Filament\Profesor\Resources\Cursos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('seccion')
                    ->required(),
                TextInput::make('cupo_maximo')
                    ->required()
                    ->numeric(),
                TextInput::make('cupo_minimo')
                    ->required()
                    ->numeric(),
                TextInput::make('cupo_actual')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('profesor_id')
                    ->numeric(),
                TextInput::make('materia_id')
                    ->numeric(),
                TextInput::make('estado_id')
                    ->numeric(),
                TextInput::make('turno_id')
                    ->numeric(),
                TextInput::make('gestion_id')
                    ->numeric(),
                Toggle::make('habilitado')
                    ->required(),
            ]);
    }
}
