<?php

namespace App\Filament\Inscripcion\Resources\Apoderados\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;

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
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Select::make('persona_id')
                            ->label('Persona')
                            ->relationship(
                                name: 'persona',
                                titleAttribute: 'nombre',
                                modifyQueryUsing: function (Builder $query, string $operation) {
                                    // Siempre ordenamos por nombre
                                    $query->orderBy('nombre');

                                    // Solo en CREATE aplicamos el filtro:
                                    // - no puede ser estudiante
                                    // - no puede ser ya apoderado
                                    if ($operation === 'create') {
                                        $query
                                            ->whereDoesntHave('estudiante')
                                            ->whereDoesntHave('apoderado');
                                    }
                                },
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
                            ->disabledOn('edit')
                            ,
                    ]),

                Section::make('Información del apoderado')
                    ->icon('heroicon-o-briefcase')
                    ->columnSpanFull()
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
