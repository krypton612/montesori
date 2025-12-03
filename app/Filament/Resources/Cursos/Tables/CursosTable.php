<?php

namespace App\Filament\Resources\Cursos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seccion')
                    ->label('Sección')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->prefix("CURSO-")
                    ->color('primary')
                    ->icon('heroicon-o-identification')
                    ->weight('bold'),

                TextColumn::make('materia.nombre')
                    ->label('Materia')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-book-open')
                    ->color('info')
                    ->wrap()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->materia?->nombre),

                TextColumn::make('profesor.nombre_completo')
                    ->label('Profesor')
                    ->searchable(['persona.nombre', 'persona.apellido_pat', 'persona.apellido_mat'])
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->color('success')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->profesor?->nombre_completo)
                    ->placeholder('Sin asignar'),

                TextColumn::make('turno.nombre')
                    ->label('Turno')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-clock')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('ocupacion')
                    ->label('Ocupación')
                    ->state(function ($record) {
                        return $record->cupo_actual . '/' . $record->cupo_maximo;
                    })
                    ->badge()
                    ->color(fn ($record) => match(true) {
                        $record->cupo_actual >= $record->cupo_maximo => 'danger',
                        $record->cupo_actual >= $record->cupo_maximo * 0.9 => 'warning',
                        $record->cupo_actual >= $record->cupo_maximo * 0.7 => 'info',
                        default => 'success',
                    })
                    ->icon(fn ($record) => match(true) {
                        $record->cupo_actual >= $record->cupo_maximo => 'heroicon-o-x-circle',
                        $record->cupo_actual >= $record->cupo_maximo * 0.9 => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('cupo_actual', $direction);
                    })
                    ->description(fn ($record) =>
                        ($record->cupo_maximo - $record->cupo_actual) . ' disponibles'
                    ),

                TextColumn::make('porcentaje')
                    ->label('% Ocupación')
                    ->state(fn ($record) =>
                    $record->cupo_maximo > 0
                        ? round(($record->cupo_actual / $record->cupo_maximo) * 100) . '%'
                        : '0%'
                    )
                    ->badge()
                    ->color(fn ($record) => match(true) {
                        ($record->cupo_actual / max($record->cupo_maximo, 1)) >= 0.9 => 'danger',
                        ($record->cupo_actual / max($record->cupo_maximo, 1)) >= 0.7 => 'warning',
                        default => 'success',
                    })
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('gestion.nombre')
                    ->label('Gestión')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-calendar')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->badge()
                    ->icon('heroicon-o-flag')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('habilitado')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-plus-circle')
                    ->color('success'),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray'),

                TextColumn::make('deleted_at')
                    ->label('Eliminado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Registros eliminados'),

                SelectFilter::make('materia_id')
                    ->label('Materia')
                    ->relationship('materia', 'nombre')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Materia'),

                SelectFilter::make('profesor_id')
                    ->label('Profesor')
                    ->relationship('profesor', 'profesor.nombre_completo')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nombre_completo)
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->indicator('Profesor'),

                SelectFilter::make('turno_id')
                    ->label('Turno')
                    ->relationship('turno', 'nombre')
                    ->multiple()
                    ->indicator('Turno'),

                SelectFilter::make('gestion_id')
                    ->label('Gestión')
                    ->relationship('gestion', 'nombre')
                    ->multiple()
                    ->indicator('Gestión'),

                Filter::make('ocupacion')
                    ->label('Nivel de Ocupación')
                    ->form([
                        \Filament\Forms\Components\Select::make('nivel')
                            ->options([
                                'completo' => '🔴 Completo (100%)',
                                'casi_lleno' => '🟡 Casi lleno (90%+)',
                                'medio' => '🟢 Medio (70-89%)',
                                'disponible' => '🔵 Disponible (<70%)',
                            ])
                            ->placeholder('Seleccionar nivel'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nivel'] ?? null,
                            function (Builder $query, $nivel) {
                                return match($nivel) {
                                    'completo' => $query->whereRaw('cupo_actual >= cupo_maximo'),
                                    'casi_lleno' => $query->whereRaw('cupo_actual >= cupo_maximo * 0.9'),
                                    'medio' => $query->whereRaw('cupo_actual >= cupo_maximo * 0.7 AND cupo_actual < cupo_maximo * 0.9'),
                                    'disponible' => $query->whereRaw('cupo_actual < cupo_maximo * 0.7'),
                                    default => $query,
                                };
                            }
                        );
                    })
                    ->indicator('Ocupación'),

                SelectFilter::make('habilitado')
                    ->label('Estado')
                    ->options([
                        '1' => 'Habilitado',
                        '0' => 'Deshabilitado',
                    ])
                    ->indicator('Estado'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye'),
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                    ForceDeleteBulkAction::make()
                        ->label('Eliminar permanentemente'),
                    RestoreBulkAction::make()
                        ->label('Restaurar seleccionados'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s') // Actualiza cada 60 segundos
            ->striped()
            ->emptyStateHeading('No hay cursos registrados')
            ->emptyStateDescription('Comienza creando un nuevo curso usando el botón superior')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
