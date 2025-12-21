<?php

namespace App\Filament\Resources\Discapacidads\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Models\Discapacidad;

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
                            ->default(function () {
                                // Generar el código automáticamente: DISC-A1, DISC-A2, etc.
                                $ultimoCodigo = Discapacidad::orderBy('id', 'desc')
                                    ->where('codigo', 'like', 'DISC-A%')
                                    ->first('codigo');
                                
                                if ($ultimoCodigo && $ultimoCodigo->codigo) {
                                    // Extraer el número del último código
                                    preg_match('/DISC-A(\d+)/', $ultimoCodigo->codigo, $matches);
                                    $numero = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
                                } else {
                                    $numero = 1;
                                }
                                
                                return 'DISC-A' . $numero;
                            })
                            ->required()
                            ->maxLength(20)
                            ->readOnly()  // Hacer el campo de solo lectura
                            ->disabled()  // Deshabilitar la edición
                            ->dehydrated(), // Asegurar que se guarde el valor

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
