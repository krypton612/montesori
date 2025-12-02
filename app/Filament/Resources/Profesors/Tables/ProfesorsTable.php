<?php

namespace App\Filament\Resources\Profesors\Tables;

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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProfesorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    ImageColumn::make('foto_url')
                        ->label('Foto')
                        ->circular()
                        ->size(60)
                        ->disk(
                            'public'
                        )
                        ->grow(false),

                    Stack::make([
                        TextColumn::make('persona.nombre_completo')
                            ->label('Profesor')
                            ->weight(FontWeight::Bold)
                            ->searchable(['persona.nombre', 'persona.apellido_pat', 'persona.apellido_mat'])
                            ->sortable(['persona.nombre', 'persona.apellido_pat'])
                            ->state(function ($record) {
                                if (!$record->persona) {
                                    return 'Sin persona asociada';
                                }
                                
                                $nombre = $record->persona->nombre ?? '';
                                $apellidoPat = $record->persona->apellido_pat ?? '';
                                $apellidoMat = $record->persona->apellido_mat ?? '';
                                
                                $nombreCompleto = trim("$nombre $apellidoPat $apellidoMat");
                                
                                return $nombreCompleto ?: 'Nombre no disponible';
                            }),

                        TextColumn::make('codigo_saga')
                            ->label('Código SAGA')
                            ->icon('heroicon-o-identification')
                            ->iconColor('primary')
                            ->badge()
                            ->color('info')
                            ->copyable()
                            ->copyMessage('Código copiado')
                            ->searchable(),
                    ])
                    ->space(2),

                    Stack::make([
                        TextColumn::make('profesion')
                            ->icon('heroicon-o-briefcase')
                            ->iconColor('success')
                            ->placeholder('Sin profesión')
                            ->limit(30),

                        TextColumn::make('persona.email_personal')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->iconColor('gray')
                            ->placeholder('Sin email')
                            ->copyable()
                            ->limit(30),

                        TextColumn::make('anios_experiencia')
                            ->label('Experiencia')
                            ->suffix(' años')
                            ->icon('heroicon-o-clock')
                            ->placeholder('N/A')
                            ->sortable()
                            ->toggleable(),

                        IconColumn::make('habilitado')
                            ->label('Estado')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->sortable(),
                            
                            ])

                        
                    ->space(2),
                ]),

                TextColumn::make('nacionalidad')
                    ->icon('heroicon-o-flag')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Sin especificar')
                    ->searchable()
                    ->toggleable(),

                

                TextColumn::make('persona.telefono_principal')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->placeholder('Sin teléfono')
                    ->toggleable(),

                TextColumn::make('documentos_count')
                    ->label('Documentos')
                    ->counts('documentosProfesores')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'warning')
                    ->icon('heroicon-o-document-text')
                    ->alignCenter()
                    ->tooltip(fn ($state) => $state > 0 
                        ? "{$state} documento(s) adjunto(s)" 
                        : 'Sin documentos'
                    )
                    ->toggleable(),

                

                TextColumn::make('created_at')
                    ->label('Registrado')
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

                SelectFilter::make('nacionalidad')
                    ->options([
                        'Boliviana' => 'Boliviana',
                        'Argentina' => 'Argentina',
                        'Brasileña' => 'Brasileña',
                        'Chilena' => 'Chilena',
                        'Colombiana' => 'Colombiana',
                        'Peruana' => 'Peruana',
                        'Venezolana' => 'Venezolana',
                        'Otra' => 'Otra',
                    ])
                    ->placeholder('Todas'),

                SelectFilter::make('tiene_documentos')
                    ->label('Documentos')
                    ->query(function ($query, $state) {
                        return match ($state['value'] ?? null) {
                            'si' => $query->has('documentosProfesores'),
                            'no' => $query->doesntHave('documentosProfesores'),
                            default => $query,
                        };
                    })
                    ->options([
                        'si' => 'Con documentos',
                        'no' => 'Sin documentos',
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
            ->emptyStateHeading('No hay profesores registrados')
            ->emptyStateDescription('Comienza agregando un nuevo profesor al sistema')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}