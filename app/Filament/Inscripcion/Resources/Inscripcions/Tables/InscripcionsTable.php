<?php

namespace App\Filament\Inscripcion\Resources\Inscripcions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class InscripcionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    // FOTO
                    ImageColumn::make('estudiante.foto_url')
                        ->disk('public')
                        ->label('Foto')
                        ->extraAttributes([
                            'style' => 'object-fit: cover; width: 100%; height: 100%; border-2;',
                        ])
                        ->height(200)           // altura completa deseada
                        ->width(200)
                        ->alignCenter()
                    ,

                    // DATOS APILADOS
                    Stack::make([
                        TextColumn::make('codigo_inscripcion')
                            ->label('Código de Inscripción')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('estudiante.persona.nombre_completo')
                            ->label('Estudiante')
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('grupo.nombre')
                            ->label('Grupo')
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('gestion.nombre')
                            ->label('Gestión')
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('fecha_inscripcion')
                            ->label('Fecha')
                            ->date()
                            ->sortable(),

                        TextColumn::make('estado.nombre')
                            ->label('Estado')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'PENDIENTE DE ENVIO' => 'warning',
                                'APROBADA'           => 'success',
                                'RECHAZADA'          => 'danger',
                                default              => 'gray',
                            })
                            ->sortable(),
                    ])
                    ->alignCenter()
                    ,
                ])->from('lg'), // Hace que el split solo se aplique en pantallas grandes
            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([

            ]);
    }
}
