<?php

namespace App\Filament\Resources\Grupos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GruposTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->icon('heroicon-o-hashtag')
                    ->iconColor('primary')
                    ->weight('bold'),

                TextColumn::make('nombre')
                    ->label('Nombre del Grupo')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string =>
                    $record->descripcion
                        ? \Illuminate\Support\Str::limit($record->descripcion, 50)
                        : 'Sin descripción'
                    )
                    ->wrap()
                    ->icon('heroicon-o-user-group')
                    ->iconColor('gray'),

                BadgeColumn::make('activo')
                    ->label('Estado')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->colors([
                        'success' => fn ($state): bool => $state === true,
                        'danger' => fn ($state): bool => $state === false,
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => fn ($state): bool => $state === true,
                        'heroicon-o-x-circle' => fn ($state): bool => $state === false,
                    ])
                    ->sortable(),

                TextColumn::make('cursos_count')
                    ->label('Cursos')
                    ->counts('cursos')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-academic-cap')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Número de cursos asignados'),

                TextColumn::make('condiciones')
                    ->label('Condiciones')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state): string =>
                    is_array($state) ? count($state) : 0
                    )
                    ->icon('heroicon-o-clipboard-document-list')
                    ->alignCenter()
                    ->tooltip('Número de condiciones definidas')
                    ->toggleable(),

                TextColumn::make('gestion_id')
                    ->label('Gestión')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-calendar')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/M/Y H:i')
                    ->sortable()
                    ->since()
                    ->description(fn ($record): string => $record->created_at->format('d/M/Y'))
                    ->icon('heroicon-o-clock')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/M/Y H:i')
                    ->sortable()
                    ->since()
                    ->description(fn ($record): string => $record->updated_at->format('d/M/Y'))
                    ->icon('heroicon-o-arrow-path')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo Activos')
                    ->falseLabel('Solo Inactivos')
                    ->native(false)
                    ->indicator('Estado'),

                SelectFilter::make('gestion_id')
                    ->label('Gestión')
                    ->relationship('gestion', 'id')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->indicator('Gestión'),

                SelectFilter::make('cursos')
                    ->label('Con Cursos')
                    ->options([
                        'con_cursos' => 'Con cursos asignados',
                        'sin_cursos' => 'Sin cursos asignados',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['value'] === 'con_cursos',
                                fn (Builder $query) => $query->has('cursos')
                            )
                            ->when(
                                $data['value'] === 'sin_cursos',
                                fn (Builder $query) => $query->doesntHave('cursos')
                            );
                    })
                    ->native(false)
                    ->indicator('Cursos'),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
                    ->label('Acciones')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->emptyStateHeading('No hay grupos registrados')
            ->emptyStateDescription('Comienza creando tu primer grupo académico.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->striped()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }
}
