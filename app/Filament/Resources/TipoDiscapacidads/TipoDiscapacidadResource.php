<?php

namespace App\Filament\Resources\TipoDiscapacidads;

use App\Filament\Resources\TipoDiscapacidads\Pages;
use App\Filament\Resources\TipoDiscapacidads\RelationManagers\DiscapacidadesRelationManager;
use App\Models\TipoDiscapacidad;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class TipoDiscapacidadResource extends Resource
{
    protected static ?string $model = TipoDiscapacidad::class;

    protected static UnitEnum|string|null $navigationGroup = 'Parametros';

    protected static ?string $modelLabel = 'Tipo de discapacidad';
    protected static ?string $pluralModelLabel = 'Tipos de discapacidad';
    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(60),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('con_descripcion')
                    ->label('Con descripción')
                    ->query(fn ($q) => $q->whereNotNull('descripcion')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DiscapacidadesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTipoDiscapacidads::route('/'),
            'create' => Pages\CreateTipoDiscapacidad::route('/create'),
            'view'   => Pages\ViewTipoDiscapacidad::route('/{record}'),
            'edit'   => Pages\EditTipoDiscapacidad::route('/{record}/edit'),
        ];
    }
}
