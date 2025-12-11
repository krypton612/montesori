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
                    ->columnSpanFull() // ocupa el ancho completo
                    ->schema([
                        Forms\Components\Select::make('persona_id')
                            ->label('Persona')
                            ->relationship(
                                'persona',
                                'nombre',
                                fn ($query) => $query
                                    ->whereDoesntHave('estudiante') // no debe ser estudiante
                                    ->whereDoesntHave('apoderado')  // ni ya apoderado
                                    ->orderBy('nombre')
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
                            ->columnSpanFull()
                            ->disabledOn('edit'), // en edición no permites cambiar la persona
                    ]),

                Section::make('Información del apoderado')
                    ->icon('heroicon-o-briefcase')
                    ->columnSpanFull() // segunda sección a todo el ancho
                    ->columns(2)       // pero interna en 2 columnas, como en tu diseño
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

                        // Convertido a SELECT
                        Forms\Components\Select::make('nivel_educacion')
                            ->label('Nivel de educación')
                            ->options([
                                'primaria'      => 'Primaria',
                                'secundaria'    => 'Secundaria',
                                'tecnico'       => 'Técnico',
                                'universitario' => 'Universitario',
                                'postgrado'     => 'Postgrado',
                            ])
                            ->native(false)
                            ->searchable()
                            ->placeholder('Seleccione una opción')
                            ->prefixIcon('heroicon-o-academic-cap'),

                        // Convertido a SELECT
                        Forms\Components\Select::make('estado_civil')
                            ->label('Estado civil')
                            ->options([
                                'soltero'     => 'Soltero(a)',
                                'casado'      => 'Casado(a)',
                                'divorciado'  => 'Divorciado(a)',
                                'viudo'       => 'Viudo(a)',
                            ])
                            ->native(false)
                            ->placeholder('Seleccione una opción')
                            ->prefixIcon('heroicon-o-heart'),
                    ]),
            ]);
    }
}
