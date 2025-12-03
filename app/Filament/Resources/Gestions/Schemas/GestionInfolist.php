<?php

namespace App\Filament\Resources\Gestions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GestionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('fecha_inicio')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('fecha_fin')
                    ->date()
                    ->placeholder('-'),
                IconEntry::make('habilitado')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
