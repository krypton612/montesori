<?php

namespace App\Filament\Resources\Cursos\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class HorariosRelationManager extends RelationManager
{
    protected static string $relationship = 'horarios';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)
                ->schema([
                    Select::make('aula_id')
                        ->label('Aula')
                        ->relationship('aula', 'codigo')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('dia')
                        ->label('Día')
                        ->options([
                            'LUNES' => 'Lunes',
                            'MARTES' => 'Martes',
                            'MIERCOLES' => 'Miércoles',
                            'JUEVES' => 'Jueves',
                            'VIERNES' => 'Viernes',
                        ])
                        ->required(),
                ]),

            Grid::make(2)
                ->schema([
                    TimePicker::make('hora_inicio')
                        ->label('Hora inicio')
                        ->required(),

                    TimePicker::make('hora_fin')
                        ->label('Hora fin')
                        ->required(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('aula.codigo')
                    ->label('Aula')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('dia')
                    ->label('Día')
                    ->sortable(),

                TextColumn::make('hora_inicio')
                    ->label('Inicio')
                    ->time(),

                TextColumn::make('hora_fin')
                    ->label('Fin')
                    ->time(),
            ])

            ->headerActions([
                CreateAction::make()->label('Agregar horario'),
            ])

            ->recordActions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar'),
            ]);
    }
}
