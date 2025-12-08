<?php

namespace App\Filament\Resources\Cursos\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
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
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                TextColumn::make('titulo')
                    ->searchable(),
                TextColumn::make('tipoEvaluacion.nombre')
                    ->label('Tipo de Evaluación')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->date(),
                TextColumn::make('fecha_fin')
                    ->date(),
                TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->searchable(),
                TextColumn::make('gestion.nombre')
                    ->label('Gestión')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([

            ])
            ->recordActions([

            ])
            ->toolbarActions([

            ]);
    }

    protected function getListeners(): array
    {
        return [
            'parentUpdated' => 'mount',
        ];
    }

}
