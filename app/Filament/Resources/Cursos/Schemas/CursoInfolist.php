<?php

namespace App\Filament\Resources\Cursos\Schemas;

use App\Models\Curso;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Schema;

class CursoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Curso')
                    ->schema([
                        Section::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('seccion')
                                        ->label('Sección')
                                        ->badge()
                                        ->weight(FontWeight::Bold)
                                        ->color('primary')
                                        ->icon('heroicon-o-identification'),

                                    TextEntry::make('gestion.nombre')
                                        ->label('Gestión')
                                        ->badge()
                                        ->color('info')
                                        ->icon('heroicon-o-calendar')
                                        ->placeholder('-'),
                                ]),

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

                Section::make('Asignación Académica')
                    ->schema([
                        TextEntry::make('materia.nombre')
                            ->label('Materia')
                            ->icon('heroicon-o-book-open')
                            ->iconColor('primary')
                            ->weight(FontWeight::SemiBold)
                            ->placeholder('Sin asignar')
                            ->color('primary'),

                        TextEntry::make('profesor.nombre_completo')
                            ->label('Profesor Titular')
                            ->icon('heroicon-o-user-circle')
                            ->iconColor('success')
                            ->placeholder('Sin asignar')
                            ->color('success'),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-academic-cap')
                    ->collapsible(),

                Section::make('Gestión de Capacidad')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('cupo_minimo')
                                    ->label('Cupo Mínimo')
                                    ->numeric()
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-arrow-down-circle')
                                    ->suffix(' estudiantes'),

                                TextEntry::make('cupo_actual')
                                    ->label('Cupo Actual')
                                    ->numeric()
                                    ->badge()
                                    ->color(fn ($state, Curso $record) => match(true) {
                                        $state >= $record->cupo_maximo * 0.9 => 'danger',
                                        $state >= $record->cupo_maximo * 0.7 => 'warning',
                                        default => 'success',
                                    })
                                    ->icon('heroicon-o-users')
                                    ->suffix(' inscritos')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('cupo_maximo')
                                    ->label('Cupo Máximo')
                                    ->numeric()
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-arrow-up-circle')
                                    ->suffix(' estudiantes'),

                                TextEntry::make('disponibilidad')
                                    ->label('Cupos Disponibles')
                                    ->state(fn (Curso $record) => $record->cupo_maximo - $record->cupo_actual)
                                    ->badge()
                                    ->color(fn ($state) => match(true) {
                                        $state <= 0 => 'danger',
                                        $state <= 5 => 'warning',
                                        default => 'success',
                                    })
                                    ->icon('heroicon-o-clipboard-document-check')
                                    ->suffix(' libres'),
                            ]),

                        TextEntry::make('porcentaje_ocupacion')
                            ->label('Porcentaje de Ocupación')
                            ->state(fn (Curso $record) =>
                            $record->cupo_maximo > 0
                                ? round(($record->cupo_actual / $record->cupo_maximo) * 100, 1)
                                : 0
                            )
                            ->suffix('%')
                            ->icon(fn ($state) => match(true) {
                                $state >= 90 => 'heroicon-o-exclamation-circle',
                                $state >= 70 => 'heroicon-o-exclamation-triangle',
                                default => 'heroicon-o-check-circle',
                            })
                            ->color(fn ($state) => match(true) {
                                $state >= 90 => 'danger',
                                $state >= 70 => 'warning',
                                default => 'success',
                            })
                            ->badge()
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->description('Monitoreo de la capacidad del curso')
                    ->collapsible(),

                Section::make('Configuración de Horario')
                    ->schema([
                        TextEntry::make('turno.nombre')
                            ->label('Turno')
                            ->badge()
                            ->color('warning')
                            ->icon('heroicon-o-clock')
                            ->placeholder('Sin turno asignado')
                            ,

                        TextEntry::make('estado.nombre')
                            ->label('Estado del Curso')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-flag')
                            ->placeholder('Sin estado'),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-calendar-days')
                    ->collapsible(),

                Section::make('Metadatos del Sistema')
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

                        TextEntry::make('deleted_at')
                            ->label('Fecha de Eliminación')
                            ->dateTime('d/m/Y H:i')
                            ->badge()
                            ->color('danger')
                            ->icon('heroicon-o-trash')
                            ->visible(fn (Curso $record): bool => $record->trashed()),
                    ])
                    ->columns(3)
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
