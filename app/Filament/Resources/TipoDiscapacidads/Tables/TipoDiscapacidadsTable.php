<?php

namespace App\Filament\Resources\TipoDiscapacidads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TipoDiscapacidadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->formatStateUsing(fn (?string $state) => $state ?: 'Sin descripción')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $textoPlano = strip_tags($state);         // quita HTML para evitar cortes rotos
                        $resumen = Str::limit($textoPlano, 50);   // corto yo manualmente
                        return "<div>{$resumen}</div>";           // regreso HTML
                    })
                    ->html()
                    ->color(fn (?string $state) => $state ? 'gray' : 'danger')
                    ->limit(10),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('con_descripcion')
                    ->label('Con descripción')
                    ->query(fn ($query) => $query->whereNotNull('descripcion')),

                Filter::make('sin_descripcion')
                    ->label('Sin descripción')
                    ->query(fn ($query) => $query->whereNull('descripcion')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
    }
}
