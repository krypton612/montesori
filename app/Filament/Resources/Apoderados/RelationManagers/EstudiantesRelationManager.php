<?php

namespace App\Filament\Resources\Apoderados\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EstudiantesRelationManager extends RelationManager
{
    // Debe coincidir con el método en App\Models\Apoderado
    protected static string $relationship = 'estudiantes';

    protected static ?string $title = 'Estudiantes';

    public function form(Schema $schema): Schema
    {
        // No usaremos formulario por ahora (no habrá create/edit desde aquí)
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Estudiante')
                    ->sortable(),

                // Si tu modelo Estudiante tiene relación persona() y campos de nombre,
                // luego puedes ajustar esto a persona.nombre_completo, etc.
                Tables\Columns\TextColumn::make('persona_id')
                    ->label('Persona ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pivot.parentestco')
                    ->label('Parentesco'),

                Tables\Columns\IconColumn::make('pivot.vive_con_el')
                    ->label('Vive con él')
                    ->boolean(),

                Tables\Columns\IconColumn::make('pivot.es_principal')
                    ->label('Apoderado principal')
                    ->boolean(),
            ])
            ->headerActions([
                // Lo dejamos sin Create para no complicar la UX de pivote por ahora
            ])
            ->actions([
                // Tampoco Edit/Delete para no tocar pivote desde aquí todavía
            ])
            ->bulkActions([
                // Sin acciones masivas de momento
            ]);
    }
}
