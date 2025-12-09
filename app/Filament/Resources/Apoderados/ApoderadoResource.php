<?php

namespace App\Filament\Resources\Apoderados;

use App\Filiment\Resources\Apoderados\Pages;
use App\Filament\Resources\Apoderados\RelationManagers\EstudiantesRelationManager;
use App\Models\Apoderado;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ApoderadoResource extends Resource
{
    protected static ?string $model = Apoderado::class;

    // Mismo tipo que la clase base (Filament 4)
    protected static UnitEnum|string|null $navigationGroup = 'Gestión Academica';
    // NO definimos $navigationIcon para no chocar con el icono del grupo

    protected static ?string $modelLabel = 'Apoderado';
    protected static ?string $pluralModelLabel = 'Apoderados';
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Forms\Components\Section::make('Datos personales')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('persona_id')
                            ->label('Persona')
                            ->relationship('persona', 'id') // luego puedes cambiar el label a nombre completo
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('estado_civil')
                            ->label('Estado civil')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Datos laborales')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('ocupacion')
                            ->label('Ocupación')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('empresa')
                            ->label('Empresa')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('cargo_empresa')
                            ->label('Cargo en la empresa')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nivel_educacion')
                            ->label('Nivel de educación')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('persona.id')
                    ->label('Persona')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ocupacion')
                    ->label('Ocupación')
                    ->searchable(),

                Tables\Columns\TextColumn::make('empresa')
                    ->label('Empresa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nivel_educacion')
                    ->label('Nivel educación')
                    ->searchable(),

                Tables\Columns\TextColumn::make('estado_civil')
                    ->label('Estado civil')
                    ->searchable(),

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
                // Aquí puedes agregar filtros (por estado civil, empresa, etc.)
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
            EstudiantesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApoderados::route('/'),
            'create' => Pages\CreateApoderado::route('/create'),
            'view'   => Pages\ViewApoderado::route('/{record}'),
            'edit'   => Pages\EditApoderado::route('/{record}/edit'),
        ];
    }
}
