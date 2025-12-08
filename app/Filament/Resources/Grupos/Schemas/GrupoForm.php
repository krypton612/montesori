<?php

namespace App\Filament\Resources\Grupos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GrupoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo')
                    ->required(),
                TextInput::make('nombre'),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                TextInput::make('condiciones'),
                Toggle::make('activo')
                    ->required(),
                TextInput::make('gestion_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
