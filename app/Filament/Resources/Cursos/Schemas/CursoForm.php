<?php

namespace App\Filament\Resources\Cursos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class CursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->schema([
                Tabs::make('Gestión del Curso')
                    ->tabs([
                        Tabs\Tab::make('Información Básica')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('seccion')
                                            ->label('Sección')
                                            ->required()
                                            ->maxLength(10)
                                            ->placeholder('Ej: A, B, 1A')
                                            ->helperText('Identificador de la sección del curso')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, $set) => $set('seccion', strtoupper($state))),

                                        Select::make('gestion_id')
                                            ->label('Gestión')
                                            ->relationship('gestion', 'nombre')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->createOptionForm([
                                                TextInput::make('nombre')
                                                    ->required()
                                                    ->label('Año de Gestión'),
                                            ]),

                                        Toggle::make('habilitado')
                                            ->label('Curso Habilitado')
                                            ->required()
                                            ->default(true)
                                            ->inline(false)
                                            ->helperText('Determina si el curso está activo para inscripciones'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Asignación Académica')
                            ->icon('heroicon-o-academic-cap')
                            ->badge(fn ($get) => ($get('materia_id') && $get('profesor_id')) ? '✓' : null)
                            ->schema([
                                Section::make('Materia y Docente')
                                    ->description('Asigna la materia y el profesor responsable del curso')
                                    ->schema([
                                        Select::make('materia_id')
                                            ->label('Materia')
                                            ->relationship('materia', 'nombre', fn ($query) => $query->where('habilitado', true))
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->live()
                                            ->createOptionForm([
                                                TextInput::make('nombre')
                                                    ->required()
                                                    ->label('Nombre de la Materia'),
                                                TextInput::make('nivel')
                                                    ->numeric()
                                                    ->label('Nivel'),
                                            ])
                                            ->columnSpanFull(),

                                        Select::make('profesor_id')
                                            ->label('Profesor Titular')
                                            ->relationship('profesor')
                                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                            $record->persona
                                                ? trim($record->persona->nombre . ' ' . $record->persona->apellido_pat . ' ' . $record->persona->apellido_mat)
                                                : 'Sin nombre'
                                            )
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search) {
                                                return \App\Models\Profesor::whereHas('persona', function ($query) use ($search) {
                                                    $query->where('nombre', 'ilike', "%{$search}%")
                                                        ->orWhere('apellido_pat', 'ilike', "%{$search}%")
                                                        ->orWhere('apellido_mat', 'ilike', "%{$search}%");
                                                })
                                                    ->with('persona')
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(fn ($profesor) => [
                                                        $profesor->id => trim($profesor->persona->nombre . ' ' .
                                                            $profesor->persona->apellido_pat . ' ' .
                                                            $profesor->persona->apellido_mat)
                                                    ]);
                                            })
                                            ->preload()
                                            ->native(false)
                                            ->placeholder('Seleccione un profesor')
                                            ->helperText('Docente encargado de impartir la materia')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),

                        Tabs\Tab::make('Configuración de Cupos')
                            ->icon('heroicon-o-users')
                            ->badge(fn ($get) => $get('cupo_actual') . '/' . $get('cupo_maximo'))
                            ->schema([
                                Section::make('Gestión de Capacidad')
                                    ->description('Define los límites de estudiantes para este curso')
                                    ->schema([
                                        TextInput::make('cupo_maximo')
                                            ->label('Cupo Máximo')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->default(30)
                                            ->suffix('estudiantes')
                                            ->helperText('Número máximo de estudiantes permitidos'),

                                        TextInput::make('cupo_minimo')
                                            ->label('Cupo Mínimo')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(10)
                                            ->suffix('estudiantes')
                                            ->helperText('Número mínimo para aperturar el curso')
                                            ->lte('cupo_maximo'),

                                        TextInput::make('cupo_actual')
                                            ->label('Cupo Actual')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('estudiantes')
                                            ->disabled()
                                            ->dehydrated()
                                            ->helperText('Estudiantes inscritos actualmente'),
                                    ])
                                    ->columns(3),

                                Section::make('Indicadores de Capacidad')
                                    ->schema([
                                        Placeholder::make('porcentaje_ocupacion')
                                            ->label('Porcentaje de Ocupación')
                                            ->content(function ($get) {
                                                $actual = $get('cupo_actual') ?? 0;
                                                $maximo = $get('cupo_maximo') ?? 1;
                                                $porcentaje = ($actual / $maximo) * 100;

                                                $color = match(true) {
                                                    $porcentaje >= 90 => '🔴',
                                                    $porcentaje >= 70 => '🟡',
                                                    default => '🟢',
                                                };

                                                return $color . ' ' . number_format($porcentaje, 1) . '%';
                                            }),

                                        Placeholder::make('cupos_disponibles')
                                            ->label('Cupos Disponibles')
                                            ->content(function ($get) {
                                                $disponibles = ($get('cupo_maximo') ?? 0) - ($get('cupo_actual') ?? 0);
                                                return '📋 ' . $disponibles . ' cupos libres';
                                            }),
                                    ])
                                    ->columns(2)
                                    ->visible(fn ($get) => $get('cupo_maximo') > 0),
                            ]),

                        Tabs\Tab::make('Horario y Estado')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Configuración de Turno')
                                    ->description('Selecciona el turno en que se dictará el curso')
                                    ->schema([
                                        Select::make('turno_id')
                                            ->label('Turno')
                                            ->relationship('turno', 'nombre', fn ($query) => $query->where('habilitado', true))
                                            ->required()
                                            ->native(false)
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Seleccione un turno')
                                            ->helperText('Horario en que se impartirá la clase')
                                            ->createOptionForm([
                                                TextInput::make('nombre')
                                                    ->required()
                                                    ->label('Nombre del Turno'),
                                            ]),
                                    ])
                                    ->columns(1),

                                Section::make('Estado del Curso')
                                    ->description('Control del estado administrativo del curso')
                                    ->schema([
                                        Select::make('estado_id')
                                            ->label('Estado')
                                            ->relationship('estado', 'nombre')
                                            ->native(false)
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Seleccione un estado')
                                            ->helperText('Estado administrativo actual del curso'),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->contained(false),
            ]);
    }
}
