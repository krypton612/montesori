<?php

namespace App\Filament\Resources\Turnos\Schemas;

use App\Models\Turno;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TurnoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Turno')
                    ->schema([
                        Section::make([
                            TextEntry::make('nombre')
                                ->label('Nombre del Turno')
                                ->weight('bold')
                                ->icon('heroicon-o-sun')
                                ->iconColor('warning'),

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

                Section::make('Horario')
                    ->schema([
                        TextEntry::make('hora_inicio')
                            ->label('Hora de Inicio')
                            ->time('H:i')
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-o-arrow-right-start-on-rectangle')
                            ->placeholder('-'),

                        TextEntry::make('hora_fin')
                            ->label('Hora de Finalización')
                            ->time('H:i')
                            ->badge()
                            ->color('danger')
                            ->icon('heroicon-o-arrow-right-end-on-rectangle')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-clock')
                    ->description(fn (Turno $record) =>
                    $record->hora_inicio && $record->hora_fin
                        ? 'Duración: ' . \Carbon\Carbon::parse($record->hora_inicio)
                            ->diffForHumans(\Carbon\Carbon::parse($record->hora_fin), ['parts' => 2])
                        : null
                    ),

                Section::make('Estado del Sistema')
                    ->schema([
                        TextEntry::make('estado.nombre')
                            ->label('Estado del turno')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-flag')
                            ->placeholder('-'),

                        TextEntry::make('deleted_at')
                            ->label('Fecha de Eliminación')
                            ->dateTime('d/m/Y H:i')
                            ->badge()
                            ->color('danger')
                            ->icon('heroicon-o-trash')
                            ->visible(fn (Turno $record): bool => $record->trashed()),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-cog-6-tooth')
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
