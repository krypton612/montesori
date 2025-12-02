<?php

namespace App\Filament\Resources\Personas\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PersonasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->searchable(['nombre', 'apellido_pat', 'apellido_mat'])
                    ->sortable(['nombre', 'apellido_pat'])
                    ->weight(FontWeight::SemiBold)
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->description(fn ($record) => $record->email_personal)
                    ->state(function ($record) {
                        return trim($record->nombre . ' ' . $record->apellido_pat . ' ' . $record->apellido_mat);
                    }),

                TextColumn::make('usuario.email')
                    ->label('Usuario Sistema')
                    ->icon('heroicon-o-at-symbol')
                    ->placeholder('Sin usuario')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('edad')
                    ->label('Edad')
                    ->suffix(' años')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('fecha_nacimiento')
                    ->label('Fecha Nacimiento')
                    ->date('d/m/Y')
                    ->icon('heroicon-o-cake')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('telefono_principal')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->copyable()
                    ->copyMessage('Teléfono copiado')
                    ->copyMessageDuration(1500)
                    ->placeholder('Sin teléfono')
                    ->toggleable(),

                TextColumn::make('telefono_secundario')
                    ->label('Tel. Secundario')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->copyable()
                    ->placeholder('Sin teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('direccion')
                    ->label('Dirección')
                    ->icon('heroicon-o-map-pin')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('habilitado')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since()
                    ->icon('heroicon-o-arrow-path'),
            ])
            ->filters([
                SelectFilter::make('habilitado')
                    ->label('Estado')
                    ->options([
                        1 => 'Habilitados',
                        0 => 'Deshabilitados',
                    ])
                    ->placeholder('Todos'),

                SelectFilter::make('tiene_usuario')
                    ->label('Usuario Sistema')
                    ->query(function ($query, $state) {
                        return match ($state['value'] ?? null) {
                            'si' => $query->whereNotNull('usuario_id'),
                            'no' => $query->whereNull('usuario_id'),
                            default => $query,
                        };
                    })
                    ->options([
                        'si' => 'Con usuario',
                        'no' => 'Sin usuario',
                    ])
                    ->placeholder('Todos'),

                TrashedFilter::make()
                    ->label('Registros eliminados'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info'),
                    EditAction::make()
                        ->color('warning'),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Acciones'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('No hay personas registradas')
            ->emptyStateDescription('Comienza agregando una nueva persona al sistema')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}