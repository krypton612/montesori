<?php

namespace App\Filament\Resources\Estudiantes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EstudiantesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('foto_url')
                    ->disk('public')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->size(60)
                    ->extraAttributes(['class' => 'ring-2 ring-primary-500']),

                TextColumn::make('persona.nombre_completo')
                    ->label('Nombre Completo')
                    ->searchable(['persona.nombre', 'persona.apellido_pat', 'persona.apellido_mat'])
                    ->sortable(['persona.nombre', 'persona.apellido_pat'])
                    ->description(fn ($record): string =>
                    $record->persona->carnet_identidad ? "CI: {$record->persona->carnet_identidad}" : 'Sin CI'
                    )
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('codigo_saga')
                    ->label('Código SAGA')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-hashtag')
                    ->placeholder('Sin código')
                    ->toggleable(),

                TextColumn::make('estado_academico')
                    ->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'activo' => 'success',
                        'inactivo' => 'gray',
                        'graduado' => 'info',
                        'retirado' => 'warning',
                        'suspendido' => 'danger',
                        'transferido' => 'purple',
                        'egresado' => 'cyan',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match($state) {
                        'activo' => 'heroicon-o-check-circle',
                        'inactivo' => 'heroicon-o-pause-circle',
                        'graduado' => 'heroicon-o-academic-cap',
                        'retirado' => 'heroicon-o-arrow-right-on-rectangle',
                        'suspendido' => 'heroicon-o-no-symbol',
                        'transferido' => 'heroicon-o-arrow-path',
                        'egresado' => 'heroicon-o-document-check',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                        'graduado' => 'Graduado',
                        'retirado' => 'Retirado',
                        'suspendido' => 'Suspendido',
                        'transferido' => 'Transferido',
                        'egresado' => 'Egresado',
                        default => ucfirst($state),
                    })
                    ->placeholder('Sin estado'),

                IconColumn::make('tiene_discapacidad')
                    ->label('Disc.')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->tooltip(fn (bool $state): string =>
                    $state ? 'Tiene discapacidad' : 'Sin discapacidad'
                    )
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('persona.edad')
                    ->label('Edad')
                    ->numeric()
                    ->suffix(' años')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-calendar')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('persona.telefono_principal')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->iconColor('green')
                    ->copyable()
                    ->copyMessage('Teléfono copiado')
                    ->placeholder('Sin teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('persona.email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->iconColor('blue')
                    ->copyable()
                    ->copyMessage('Email copiado')
                    ->placeholder('Sin email')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                TextColumn::make('apoderados_count')
                    ->label('Apoderados')
                    ->counts('apoderados')
                    ->badge()
                    ->color('purple')
                    ->icon('heroicon-o-users')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip('Número de apoderados registrados')
                    ->toggleable(),

                TextColumn::make('discapacidades_count')
                    ->label('Disc. Reg.')
                    ->counts('discapacidades')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip('Discapacidades registradas')
                    ->toggleable()
                    ->visible(fn () => true),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/M/Y')
                    ->sortable()
                    ->since()
                    ->description(fn ($record): string => $record->created_at->format('d/m/Y H:i'))
                    ->icon('heroicon-o-clock')
                    ->iconColor('success')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/M/Y')
                    ->sortable()
                    ->since()
                    ->description(fn ($record): string => $record->updated_at->format('d/m/Y H:i'))
                    ->icon('heroicon-o-arrow-path')
                    ->iconColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Eliminado')
                    ->dateTime('d/M/Y H:i')
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make()
                    ->label('Estado de Registro')
                    ->placeholder('Todos')
                    ->trueLabel('Solo Eliminados')
                    ->falseLabel('Sin Eliminados')
                    ->native(false),




            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                    RestoreBulkAction::make()
                        ->icon('heroicon-o-arrow-path')
                        ->color('success'),
                    ForceDeleteBulkAction::make()
                        ->icon('heroicon-o-x-circle')
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
                    RestoreBulkAction::make()
                        ->icon('heroicon-o-arrow-path'),
                    ForceDeleteBulkAction::make()
                        ->icon('heroicon-o-x-circle'),
                ]),
            ])
            ->emptyStateHeading('No hay estudiantes registrados')
            ->emptyStateDescription('Comienza agregando tu primer estudiante al sistema.')
            ->emptyStateIcon('heroicon-o-user-plus')
            ->striped()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->persistColumnSearchesInSession()
            ->paginated([10, 25, 50, 100, 200])
            ->poll('60s')
            ->deferLoading()
            ->extremePaginationLinks();
    }
}
