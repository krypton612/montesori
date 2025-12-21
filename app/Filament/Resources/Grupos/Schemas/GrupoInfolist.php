<?php

namespace App\Filament\Resources\Grupos\Schemas;

use App\Models\Grupo;
use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Schema;

class GrupoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General del Grupo')
                    ->description('Datos principales del grupo académico')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('codigo')
                                    ->label('Código')
                                    ->badge()
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-o-hashtag')
                                    ->copyable()
                                    ->copyMessage('Código copiado')
                                    ->copyMessageDuration(1500),

                                TextEntry::make('gestion_id')
                                    ->label('Gestión')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('-')
                                    ->weight(FontWeight::SemiBold),

                                IconEntry::make('activo')
                                    ->label('Estado')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->grow(false),
                            ]),

                        TextEntry::make('nombre')
                            ->label('Nombre del Grupo')
                            ->icon('heroicon-o-identification')
                            ->iconColor('primary')
                            ->weight(FontWeight::SemiBold)
                            ->placeholder('Sin nombre asignado')
                            ->color('primary')
                            ->columnSpanFull(),

                        TextEntry::make('descripcion')
                            ->label('Descripción')
                            ->icon('heroicon-o-document-text')
                            ->iconColor('gray')
                            ->placeholder('Sin descripción')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Section::make('Metadatos del Sistema')
                    ->schema([
                        Grid::make(3)
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
                                    ->icon('heroicon-o-trash'),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),
                Section::make('Cursos Asignados')
                    ->description('Lista de cursos que pertenecen a este grupo')
                    ->columnSpanFull()
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        RepeatableEntry::make('cursos')
                            ->label('')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('materia.grado')
                                            ->label('Grado')
                                            ->badge()
                                            ->color('primary')
                                            ->icon('heroicon-o-chart-bar')
                                            ->suffix('°')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('materia.nombre')
                                            ->label('Materia')
                                            ->icon('heroicon-o-book-open')
                                            ->iconColor('success')
                                            ->weight(FontWeight::SemiBold)
                                            ->color('success')
                                            ->columnSpan(2),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('seccion')
                                            ->label('Sección')
                                            ->badge()
                                            ->color('info')
                                            ->icon('heroicon-o-identification'),

                                        TextEntry::make('turno.nombre')
                                            ->label('Turno')
                                            ->badge()
                                            ->color('warning')
                                            ->icon('heroicon-o-clock')
                                            ->placeholder('Sin turno'),

                                        IconEntry::make('habilitado')
                                            ->label('Activo')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-check-circle')
                                            ->falseIcon('heroicon-o-x-circle')
                                            ->trueColor('success')
                                            ->falseColor('danger'),

                                        TextEntry::make('cupo_actual')
                                            ->label('Inscritos')
                                            ->numeric()
                                            ->badge()
                                            ->color(fn ($record) => match(true) {
                                                $record->cupo_actual >= $record->cupo_maximo * 0.9 => 'danger',
                                                $record->cupo_actual >= $record->cupo_maximo * 0.7 => 'warning',
                                                default => 'success',
                                            })
                                            ->icon('heroicon-o-users')
                                            ->suffix(fn ($record) => " / {$record->cupo_maximo}"),
                                    ]),

                                TextEntry::make('profesor.nombre_completo')
                                    ->label('Profesor Titular')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('purple')
                                    ->placeholder('Sin profesor asignado')
                                    ->color('purple')
                                    ->weight(FontWeight::Medium)
                                    ->columnSpanFull(),

                                Section::make('Datos del Profesor')

                                    ->schema([
                                        Grid::make(3)
                                            ->columnSpanFull()
                                            ->schema([
                                                TextEntry::make('profesor.codigo_saga')
                                                    ->label('Código SAGA')
                                                    ->badge()
                                                    ->color('gray')
                                                    ->icon('heroicon-o-hashtag')
                                                    ->placeholder('-'),

                                                TextEntry::make('profesor.nacionalidad')
                                                    ->label('Nacionalidad')
                                                    ->icon('heroicon-o-flag')
                                                    ->placeholder('-'),

                                                TextEntry::make('profesor.anios_experiencia')
                                                    ->label('Experiencia')
                                                    ->numeric()
                                                    ->suffix(' años')
                                                    ->badge()
                                                    ->color('info')
                                                    ->icon('heroicon-o-academic-cap')
                                                    ->placeholder('-'),
                                            ]),

                                        TextEntry::make('profesor.profesion')
                                            ->label('Profesión')
                                            ->icon('heroicon-o-briefcase')
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->collapsed()
                                    ->visible(fn ($record) => $record->profesor_id !== null),

                                Section::make('Detalles de la Materia')
                                    ->schema([
                                        Grid::make(3)
                                            ->columnSpanFull()
                                            ->schema([
                                                TextEntry::make('materia.nivel')
                                                    ->label('Complejidad Nivel')
                                                    ->badge()
                                                    ->color('primary')
                                                    ->icon('heroicon-o-signal')
                                                    ->placeholder('-'),

                                                TextEntry::make('materia.horas_semanales')
                                                    ->label('Horas Semanales')
                                                    ->numeric()
                                                    ->suffix(' hrs')
                                                    ->icon('heroicon-o-clock')
                                                    ->placeholder('-'),

                                                IconEntry::make('materia.habilitado')
                                                    ->label('Materia Habilitada')
                                                    ->boolean()
                                                    ->trueIcon('heroicon-o-check-circle')
                                                    ->falseIcon('heroicon-o-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger'),
                                            ]),

                                        TextEntry::make('materia.descripcion')
                                            ->label('Descripción de la Materia')
                                            ->placeholder('Sin descripción')
                                            ->markdown()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->collapsed(),
                            ])
                            ->contained(true)
                            ->columnSpanFull(),
                    ])
                    ->headerActions([

                    ])
                    ->collapsible(),

                Section::make('Condiciones y Requisitos')
                    ->description('Criterios de elegibilidad para este grupo')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        RepeatableEntry::make('condiciones')
                            ->label('')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('tipo')
                                            ->label('Tipo de Condición')
                                            ->badge()
                                            ->color(fn (string $state): string => match($state) {
                                                'edad' => 'info',
                                                'promedio' => 'success',
                                                'asistencia' => 'warning',
                                                'prerequisito' => 'danger',
                                                'nivel' => 'primary',
                                                default => 'gray',
                                            })
                                            ->icon(fn (string $state): string => match($state) {
                                                'edad' => 'heroicon-o-user',
                                                'promedio' => 'heroicon-o-chart-bar',
                                                'asistencia' => 'heroicon-o-calendar',
                                                'prerequisito' => 'heroicon-o-book-open',
                                                'nivel' => 'heroicon-o-academic-cap',
                                                default => 'heroicon-o-cog',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match($state) {
                                                'edad' => '👶 Edad',
                                                'promedio' => '📊 Promedio',
                                                'asistencia' => '📅 Asistencia',
                                                'prerequisito' => '📚 Pre-requisito',
                                                'nivel' => '🎓 Nivel',
                                                'otro' => '⚙️ Otro',
                                                default => ucfirst($state),
                                            }),

                                        TextEntry::make('valor')
                                            ->label('Valor')
                                            ->icon('heroicon-o-variable')
                                            ->weight(FontWeight::SemiBold)
                                            ->color('primary'),

                                        TextEntry::make('operador')
                                            ->label('Operador')
                                            ->badge()
                                            ->color('gray')
                                            ->formatStateUsing(fn (string $state): string => match($state) {
                                                'mayor' => 'Mayor que (>)',
                                                'menor' => 'Menor que (<)',
                                                'igual' => 'Igual a (=)',
                                                'mayor_igual' => 'Mayor o igual (≥)',
                                                'menor_igual' => 'Menor o igual (≤)',
                                                'entre' => 'Entre',
                                                'ninguno' => 'No aplica',
                                                default => $state,
                                            }),
                                    ]),

                                TextEntry::make('descripcion')
                                    ->label('Descripción')
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('Sin descripción adicional')
                                    ->markdown()
                                    ->columnSpanFull(),

                                IconEntry::make('obligatorio')
                                    ->label('¿Es Obligatorio?')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-exclamation-circle')
                                    ->falseIcon('heroicon-o-information-circle')
                                    ->trueColor('danger')
                                    ->falseColor('info'),
                            ])
                            ->contained(true)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Grupo $record): bool =>
                        is_array($record->condiciones) && count($record->condiciones) > 0
                    )
                    ->collapsible()
                    ->collapsed(),

                Section::make('Estadísticas del Grupo')
                    ->description('Resumen de datos relevantes')
                    ->icon('heroicon-o-chart-pie')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('total_cursos')
                                    ->label('Total de Cursos')
                                    ->state(fn (Grupo $record) => $record->cursos->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-academic-cap'),
                                TextEntry::make('cursos_activos')
                                    ->label('Cursos Activos')
                                    ->state(fn (Grupo $record) => $record->cursos->where('habilitado', true)->count())
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-check-circle'),
                                TextEntry::make('total_estudiantes')
                                    ->label('Total Estudiantes')
                                    ->state(fn (Grupo $record) => $record->cursos->sum('cupo_actual'))
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-users'),
                                TextEntry::make('capacidad_total')
                                    ->label('Capacidad Total')
                                    ->state(fn (Grupo $record) => $record->cursos->sum('cupo_maximo'))
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-chart-bar')]),

                        TextEntry::make('ocupacion_promedio')
                            ->label('Porcentaje de Ocupación Promedio')
                            ->state(function (Grupo $record) {
                                $totalCupoMaximo = $record->cursos->sum('cupo_maximo');
                                $totalCupoActual = $record->cursos->sum('cupo_actual');

                                return $totalCupoMaximo > 0
                                    ? round(($totalCupoActual / $totalCupoMaximo) * 100, 1)
                                    : 0;
                            })
                            ->suffix('%')
                            ->badge()
                            ->color(fn ($state) => match(true) {
                                $state >= 90 => 'danger',
                                $state >= 70 => 'warning',
                                $state >= 50 => 'success',
                                default => 'gray',
                            })
                            ->icon(fn ($state) => match(true) {
                                $state >= 90 => 'heroicon-o-exclamation-circle',
                                $state >= 70 => 'heroicon-o-exclamation-triangle',
                                default => 'heroicon-o-check-circle',
                            })
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),


            ]);
    }
}
