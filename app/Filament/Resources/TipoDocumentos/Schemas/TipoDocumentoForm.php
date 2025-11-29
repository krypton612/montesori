<?php

namespace App\Filament\Resources\TipoDocumentos\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TipoDocumentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Textarea::make('descripcion')->maxLength(3000),
                Toggle::make('habilitado')
                    ->required(),
                TextInput::make('tipo')
                    ->required(),
            ]);
    }
}
