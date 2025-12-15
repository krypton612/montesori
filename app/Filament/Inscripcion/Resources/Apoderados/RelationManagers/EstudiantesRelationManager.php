<?php

namespace App\Filament\Resources\Apoderados\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\{CreateAction, EditAction, DeleteAction, ViewAction};

class EstudiantesRelationManager extends RelationManager
{
    protected static string $relationship = 'estudiantes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('estudiante_id')
                ->label('Estudiante')
                ->relationship('estudiante', 'codigo_saga') // atributo del modelo Estudiante
                ->searchable()
                ->required(),

            TextInput::make('parentestco')
                ->label('Parentesco')
                ->required()
                ->maxLength(255),

            Toggle::make('vive_con_el')
                ->label('Vive con el apoderado')
                ->default(false),

            Toggle::make('es_principal')
                ->label('Apoderado principal')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('estudiante.codigo_saga')
                    ->label('Código SAGA')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('estudiante.persona.nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('parentestco')
                    ->label('Parentesco'),

                IconColumn::make('vive_con_el')
                    ->label('Vive con él')
                    ->boolean(),

                IconColumn::make('es_principal')
                    ->label('Principal')
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
