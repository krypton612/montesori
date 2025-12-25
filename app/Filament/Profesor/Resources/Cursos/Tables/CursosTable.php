<?php

namespace App\Filament\Profesor\Resources\Cursos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seccion')
                    ->searchable(),
                TextColumn::make('cupo_maximo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cupo_minimo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cupo_actual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('profesor_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('materia_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estado_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('turno_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gestion_id')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('habilitado')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
