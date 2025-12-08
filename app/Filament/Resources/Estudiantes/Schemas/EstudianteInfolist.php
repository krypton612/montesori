<?php

namespace App\Filament\Resources\Estudiantes\Schemas;

use App\Models\Estudiante;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EstudianteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('persona_id')
                    ->numeric(),
                TextEntry::make('codigo_saga')
                    ->placeholder('-'),
                TextEntry::make('estado_academico')
                    ->placeholder('-'),
                IconEntry::make('tiene_discapacidad')
                    ->boolean(),
                TextEntry::make('observaciones')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('foto_url')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Estudiante $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
