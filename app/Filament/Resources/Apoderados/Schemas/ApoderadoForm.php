<?php

namespace App\Filament\Resources\Apoderados\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ApoderadoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Datos de la persona')
                    ->icon('heroicon-o-user')
                    ->description('Selecciona la persona asociada a este apoderado')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Select::make('persona_id')
                            ->label('Persona')
                            ->relationship(
                                'persona',
                                'nombre',
                                fn ($query) => $query->orderBy('nombre')
                            )
                            ->searchable(['nombre', 'apellido_pat', 'apellido_mat', 'email_personal'])
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) =>
                                    trim($record->nombre . ' ' . $record->apellido_pat . ' ' . $record->apellido_mat)
                                    . ($record->email_personal ? ' · ' . $record->email_personal : '')
                            )
                            ->native(false)
                            ->preload()
                            ->required()
                            ->helperText('Busca por nombre, apellidos o correo.')
                            ->prefixIcon('heroicon-o-user-circle')
                            ->columnSpanFull(),
                    ]),

                Section::make('Información del apoderado')
                    ->icon('heroicon-o-briefcase')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('ocupacion')
                            ->label('Ocupación')
                            ->maxLength(255)
                            ->placeholder('Ej: Ingeniero, Comerciante, Ama de casa')
                            ->prefixIcon('heroicon-o-briefcase'),

                        Forms\Components\TextInput::make('empresa')
                            ->label('Empresa')
                            ->maxLength(255)
                            ->placeholder('Nombre de la empresa')
                            ->prefixIcon('heroicon-o-building-office'),

                        Forms\Components\TextInput::make('cargo_empresa')
                            ->label('Cargo')
                            ->maxLength(255)
                            ->placeholder('Ej: Jefe de área')
                            ->prefixIcon('heroicon-o-briefcase'),

                        Forms\Components\TextInput::make('nivel_educacion')
                            ->label('Nivel de educación')
                            ->maxLength(255)
                            ->placeholder('Ej: Licenciatura, Técnico, Secundaria')
                            ->prefixIcon('heroicon-o-academic-cap'),

                        Forms\Components\TextInput::make('estado_civil')
                            ->label('Estado civil')
                            ->maxLength(255)
                            ->placeholder('Ej: Soltero, Casado, Divorciado')
                            ->prefixIcon('heroicon-o-heart'),
                    ]),
            ]);
    }
}
