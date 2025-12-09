<?php

namespace App\Filament\Resources\TipoDiscapacidads\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class DiscapacidadesRelationManager extends RelationManager
{
    // 👇 Debe coincidir con la relación en el modelo TipoDiscapacidad
    protected static string $relationship = 'discapacidades';

    protected static ?string $title = 'Discapacidades';

    /**
     * IMPORTANTE:
     * En tu versión de Filament, form() es de instancia y recibe Schema.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('codigo')
                    ->label('Código')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('requiere_acompaniante')
                    ->label('Requiere acompañante')
                    ->default(false),

                Toggle::make('necesita_equipo_especial')
                    ->label('Necesita equipo especial')
                    ->default(false),

                Toggle::make('requiere_adaptacion_curricular')
                    ->label('Requiere adaptación curricular')
                    ->default(false),

                Toggle::make('visible')
                    ->label('Visible')
                    ->default(true),
            ]);
    }

    /**
     * También es de instancia en tu versión.
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

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
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
