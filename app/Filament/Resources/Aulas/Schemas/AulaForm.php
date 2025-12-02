<?php

namespace App\Filament\Resources\Aulas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AulaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo')
                    ->required(),
                TextInput::make('numero'),
                TextInput::make('capacidad')
                    ->numeric(),
                Toggle::make('habilitado')
                    ->required(),
            ]);
    }
}
