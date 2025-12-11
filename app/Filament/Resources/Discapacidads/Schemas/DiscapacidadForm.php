<?php

namespace App\Filament\Resources\Discapacidads\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class DiscapacidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Información de la discapacidad')
                    ->icon('heroicon-o-heart')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->maxLength(50),

                        Forms\Components\Select::make('tipo_discapacidad_id')
                            ->label('Tipo de discapacidad')
                            ->relationship('tipoDiscapacidad', 'nombre')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->required(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Requerimientos')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        Forms\Components\Toggle::make('requiere_acompaniante')
                            ->label('Requiere acompañante'),

                        Forms\Components\Toggle::make('necesita_equipo_especial')
                            ->label('Necesita equipo especial'),

                        Forms\Components\Toggle::make('requiere_adaptacion_curricular')
                            ->label('Requiere adaptación curricular'),

                        Forms\Components\Toggle::make('visible')
                            ->label('Visible en el sistema')
                            ->default(true),
                    ]),
            ]);
    }
}
