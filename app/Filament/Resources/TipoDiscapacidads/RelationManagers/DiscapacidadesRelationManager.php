<?php

namespace App\Filament\Resources\TipoDiscapacidads\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DiscapacidadesRelationManager extends RelationManager
{
    protected static string $relationship = 'discapacidades';

    protected static ?string $title = 'Discapacidades';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('codigo')
                    ->label('Código')
                    ->required()
                    ->maxLength(50),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('visible')
                    ->label('Visible')
                    ->default(true),

                Forms\Components\Toggle::make('requiere_acompaniante')
                    ->label('Requiere acompañante'),

                Forms\Components\Toggle::make('necesita_equipo_especial')
                    ->label('Necesita equipo especial'),

                Forms\Components\Toggle::make('requiere_adaptacion_curricular')
                    ->label('Requiere adaptación curricular'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('visible')
                    ->label('Visible')
                    ->boolean(),

                Tables\Columns\IconColumn::make('requiere_acompaniante')
                    ->label('Acompañante')
                    ->boolean(),

                Tables\Columns\IconColumn::make('necesita_equipo_especial')
                    ->label('Equipo especial')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('requiere_adaptacion_curricular')
                    ->label('Adaptación curricular')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
