<?php

namespace App\Filament\Resources\TipoDiscapacidads;

use App\Filament\Resources\TipoDiscapacidads\Pages\ManageTipoDiscapacidads;
use App\Filament\Resources\TipoDiscapacidads\RelationManagers\DiscapacidadesRelationManager;
use App\Models\TipoDiscapacidad;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TipoDiscapacidadResource extends Resource
{
    protected static ?string $model = TipoDiscapacidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Parametros';

    protected static ?string $navigationLabel = 'Tipos de discapacidad';

    protected static ?string $modelLabel = 'Tipo de discapacidad';

    protected static ?string $pluralModelLabel = 'Tipos de discapacidad';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre')
                    ->label('Nombre'),

                TextEntry::make('descripcion')
                    ->label('Descripción')
                    ->placeholder('-'),

                TextEntry::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(60),

                TextColumn::make('discapacidades_count')
                    ->label('N° discapacidades')
                    ->counts('discapacidades')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ManageTipoDiscapacidads::route('/'),
        ];
    }
}
