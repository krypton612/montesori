<?php

namespace App\Filament\Inscripcion\Resources\Apoderados\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;

class ApoderadoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la persona')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('persona.nombre')
                                ->label('Nombre')
                                ->icon('heroicon-o-user')
                                ->weight(FontWeight::SemiBold),

                            TextEntry::make('persona.apellido_pat')
                                ->label('Apellido paterno')
                                ->weight(FontWeight::SemiBold),

                            TextEntry::make('persona.apellido_mat')
                                ->label('Apellido materno')
                                ->placeholder('-'),

                            TextEntry::make('persona.email_personal')
                                ->label('Correo')
                                ->icon('heroicon-o-envelope')
                                ->copyable()
                                ->placeholder('Sin correo'),

                            TextEntry::make('persona.telefono_principal')
                                ->label('Teléfono principal')
                                ->icon('heroicon-o-phone')
                                ->placeholder('Sin teléfono'),

                            TextEntry::make('persona.direccion')
                                ->label('Dirección')
                                ->icon('heroicon-o-map-pin')
                                ->placeholder('Sin dirección')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Información del apoderado')
                    ->icon('heroicon-o-briefcase')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('ocupacion')
                            ->label('Ocupación')
                            ->placeholder('-'),

                        TextEntry::make('empresa')
                            ->label('Empresa')
                            ->placeholder('-'),

                        TextEntry::make('cargo_empresa')
                            ->label('Cargo')
                            ->placeholder('-'),

                        TextEntry::make('nivel_educacion')
                            ->label('Nivel de educación')
                            ->badge()
                            ->color('info')
                            ->placeholder('-'),

                        TextEntry::make('estado_civil')
                            ->label('Estado civil')
                            ->badge()
                            ->color('success')
                            ->placeholder('-'),
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
