<?php

namespace App\Filament\Resources\Discapacidads\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class DiscapacidadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la discapacidad')
                    ->icon('heroicon-o-heart')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('nombre')
                                ->label('Nombre')
                                ->weight(FontWeight::SemiBold),

                            TextEntry::make('codigo')
                                ->label('Código')
                                ->placeholder('-'),

                            TextEntry::make('tipoDiscapacidad.nombre')
                                ->label('Tipo')
                                ->placeholder('-')
                                ->columnSpanFull(),

                            TextEntry::make('descripcion')
                                ->label('Descripción')
                                ->columnSpanFull()
                                ->placeholder('-'),
                        ]),
                    ]),

                Section::make('Requerimientos')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->columns(4)
                    ->schema([
                        IconEntry::make('requiere_acompaniante')
                            ->label('Requiere acompañante')
                            ->boolean(),

                        IconEntry::make('necesita_equipo_especial')
                            ->label('Equipo especial')
                            ->boolean(),

                        IconEntry::make('requiere_adaptacion_curricular')
                            ->label('Adaptación curricular')
                            ->boolean(),

                        IconEntry::make('visible')
                            ->label('Visible')
                            ->boolean(),
                    ]),

                Section::make('Metadatos')
                    ->icon('heroicon-o-information-circle')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->since(),

                        TextEntry::make('deleted_at')
                            ->label('Eliminado')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('No eliminado'),
                    ]),
            ]);
    }
}
