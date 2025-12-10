<?php

namespace App\Filament\Resources\TipoDiscapacidads\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TipoDiscapacidadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre')
                    ->label('Nombre'),

                TextEntry::make('descripcion')
                    ->label('Descripción')
                    ->placeholder('-'),

                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ]);
    }
}
