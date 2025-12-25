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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'evaluaciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('titulo')
                    ->required(),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                DatePicker::make('fecha_inicio'),
                DatePicker::make('fecha_fin'),
                TextInput::make('tipo_evaluacion_id')
                    ->required()
                    ->numeric(),
                TextInput::make('estado_id')
                    ->required()
                    ->numeric(),
                TextInput::make('gestion_id')
                    ->required()
                    ->numeric(),
                Toggle::make('visible')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                TextColumn::make('titulo')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->date()
                    ->sortable(),
                TextColumn::make('tipo_evaluacion_id')
                    ->numeric()
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
                TextColumn::make('gestion_id')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('visible')
                    ->boolean(),
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
