<?php

namespace App\Filament\Resources\Apoderados\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;

class ApoderadosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('persona.nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('persona.apellido_pat')
                    ->label('Apellido paterno')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('persona.telefono_principal')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->searchable(),

                TextColumn::make('persona.email_personal')
                    ->label('Correo')
                    ->icon('heroicon-o-envelope')
                    ->searchable(),

                TextColumn::make('nivel_educacion')
                    ->label('Educación')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('estado_civil')
                    ->label('Estado civil')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'Soltero', 'Soltera'     => 'gray',
                        'Casado', 'Casada'       => 'success',
                        'Divorciado', 'Divorciada' => 'warning',
                        'Viudo', 'Viuda'         => 'danger',
                        default                  => 'primary',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('estudiantes_count')
                    ->label('Estudiantes')
                    ->counts('estudiantes')   // requiere relación estudiantes() en el modelo
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
