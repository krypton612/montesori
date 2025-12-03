<?php

namespace App\Filament\Resources\Cursos\Schemas;

use App\Models\Curso;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CursoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('seccion'),
                TextEntry::make('cupo_maximo')
                    ->numeric(),
                TextEntry::make('cupo_minimo')
                    ->numeric(),
                TextEntry::make('cupo_actual')
                    ->numeric(),
                TextEntry::make('profesor_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('materia_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('estado_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('turno_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('gestion_id')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('habilitado')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Curso $record): bool => $record->trashed()),
            ]);
    }
}
