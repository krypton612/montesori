<?php

namespace App\Filament\Resources\Materias\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MateriaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('nivel')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('horas_semanales')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('descripcion')
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
