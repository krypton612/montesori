<?php

namespace App\Filament\Resources\Materias\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MateriaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->schema([
                        Section::make([
                            TextEntry::make('nombre')
                                ->label('Nombre')
                                ->weight('bold')
                                ->icon('heroicon-o-academic-cap')
                                ->iconColor('primary'),

                            IconEntry::make('habilitado')
                                ->label('Estado')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger')
                                ->grow(false),
                        ]),
                    ])
                    ->columns(1),

                Section::make('Detalles Académicos')
                    ->schema([
                        TextEntry::make('nivel')
                            ->label('Nivel')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-arrow-trending-up')
                            ->placeholder('-'),

                        TextEntry::make('horas_semanales')
                            ->label('Horas Semanales')
                            ->badge()
                            ->color('warning')
                            ->icon('heroicon-o-clock')
                            ->suffix(' hrs')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-book-open')
                    ->collapsible(),

                Section::make('Descripción')
                    ->schema([
                        TextEntry::make('descripcion')
                            ->label('Descripción de la Materia')
                            ->prose()
                            ->placeholder('Sin descripción')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Metadatos')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-o-plus-circle')
                            ->color('success')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-o-arrow-path')
                            ->color('gray')
                            ->since()
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
