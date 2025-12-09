<?php

namespace App\Filament\Resources\Apoderados\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ApoderadoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos personales')
                    ->schema([
                        TextEntry::make('persona.id')
                            ->label('Persona'),

                        TextEntry::make('estado_civil')
                            ->label('Estado civil')
                            ->placeholder('Sin especificar'),
                    ])
                    ->columns(2),

                Section::make('Datos laborales')
                    ->schema([
                        TextEntry::make('ocupacion')
                            ->label('Ocupación')
                            ->placeholder('Sin especificar'),

                        TextEntry::make('empresa')
                            ->label('Empresa')
                            ->placeholder('Sin especificar'),

                        TextEntry::make('cargo_empresa')
                            ->label('Cargo'),

                        TextEntry::make('nivel_educacion')
                            ->label('Nivel de educación'),
                    ])
                    ->columns(2),

                Section::make('Meta')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
