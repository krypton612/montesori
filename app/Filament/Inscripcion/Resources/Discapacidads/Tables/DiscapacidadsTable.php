<?php

namespace App\Filament\Inscripcion\Resources\Discapacidads\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\{DeleteAction, EditAction, ViewAction};
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class DiscapacidadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tipoDiscapacidad.nombre')
                    ->label('Tipo')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('requiere_acompaniante')
                    ->label('Acompañante')
                    ->boolean(),

                IconColumn::make('necesita_equipo_especial')
                    ->label('Equipo especial')
                    ->boolean(),

                IconColumn::make('requiere_adaptacion_curricular')
                    ->label('Adaptación curricular')
                    ->boolean(),

                IconColumn::make('visible')
                    ->label('Visible')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('visible')
                    ->label('Visible'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
