<?php

namespace App\Filament\Profesor\Resources\Cursos\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InscripcionesRelationManager extends RelationManager
{
    protected static string $relationship = 'inscripciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo_inscripcion')
                    ->required(),
                TextInput::make('estudiante_id')
                    ->required()
                    ->numeric(),
                TextInput::make('grupo_id')
                    ->numeric(),
                TextInput::make('gestion_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('fecha_inscripcion')
                    ->required(),
                TextInput::make('estado_id')
                    ->required()
                    ->numeric(),
                TextInput::make('condiciones'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo_inscripcion')
            ->columns([
                TextColumn::make('codigo_inscripcion')
                    ->searchable(),
                TextColumn::make('estudiante_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('grupo_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gestion_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fecha_inscripcion')
                    ->date()
                    ->sortable(),
                TextColumn::make('estado_id')
                    ->numeric()
                    ->sortable(),
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
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
