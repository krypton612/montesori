<?php

namespace App\Filament\Resources\Grupos\Schemas;

use App\Models\Gestion;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class GrupoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Datos básicos del grupo')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('codigo')
                                    ->label('Código')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Ej: GRUPO-2024-A')
                                    ->helperText('Código único identificador del grupo')
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->autocomplete(false),

                                Select::make('gestion_id')
                                    ->label('Gestión')
                                    ->relationship('gestion', 'nombre')
                                    ->default(function () {
                                        $currentYear = date('Y');
                                        $gestion = Gestion::where('nombre', $currentYear)->first();
                                        return $gestion ? $gestion->id : null;
                                    })
                                    ->required()
                                    ->placeholder('2024')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->helperText('Año de gestión académica')
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->label('Año de Gestión'),
                                    ]),
                            ]),

                        TextInput::make('nombre')
                            ->label('Nombre del Grupo')
                            ->maxLength(255)
                            ->placeholder('Ej: Grupo Avanzado de Matemáticas')
                            ->prefixIcon('heroicon-o-user-group')
                            ->columnSpanFull(),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Describe el propósito y características de este grupo...')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Toggle::make('activo')
                            ->label('Estado Activo')
                            ->helperText('Activa o desactiva este grupo')
                            ->inline(false)
                            ->default(true)
                            ->required(),
                    ]),

                Section::make('Cursos Asignados')
                    ->description('Selecciona los cursos que pertenecen a este grupo')
                    ->icon('heroicon-o-academic-cap')
                    ->collapsible()
                    ->schema([
                        Select::make('cursos')
                            ->label('Cursos')
                            ->multiple()
                            ->preload()
                            ->relationship(
                                'cursos',
                                'seccion',
                                fn ($query) => $query->join('materia', 'curso.materia_id', '=', 'materia.id')
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) =>
                            "{$record->materia->grado}° - {$record->materia->nombre} (Codigo {$record->seccion})"
                            )
                            ->searchable(['curso.seccion', 'materia.nombre', 'materia.grado'])
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\Curso::query()
                                    ->join('materia', 'curso.materia_id', '=', 'materia.id')
                                    ->where(function ($query) use ($search) {
                                        $query->where('curso.seccion', 'like', "%{$search}%")
                                            ->orWhere('materia.nombre', 'like', "%{$search}%")
                                            ->orWhere('materia.grado', 'like', "%{$search}%");
                                    })
                                    ->select('curso.*')
                                    ->with('materia')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($curso) => [
                                        $curso->id => "{$curso->materia->grado}° - {$curso->materia->nombre} (Sección {$curso->seccion})"
                                    ]);
                            })
                            ->placeholder('Selecciona uno o más cursos')
                            ->helperText('Puedes buscar por grado, nombre de materia o sección')
                            ->columnSpanFull()
                            ->native(false),
                    ]),

                Section::make('Condiciones y Requisitos')
                    ->description('Define las condiciones específicas para este grupo')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('condiciones')
                            ->label('Condiciones')

                            ->schema([
                                Select::make('tipo')
                                    ->label('Tipo de Condición')
                                    ->options([
                                        'edad' => '👶 Edad',
                                        'promedio' => '📊 Promedio Académico',
                                        'asistencia' => '📅 Asistencia',
                                        'prerequisito' => '📚 Pre-requisito',
                                        'nivel' => '🎓 Nivel Académico',
                                        'otro' => '⚙️ Otro',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('valor')
                                            ->label('Valor/Descripción')
                                            ->required()
                                            ->placeholder('Ej: Mínimo 70%, Mayor a 15 años, etc.')
                                            ->maxLength(255),

                                        Select::make('operador')
                                            ->label('Operador')
                                            ->options([
                                                'mayor' => 'Mayor que (>)',
                                                'menor' => 'Menor que (<)',
                                                'igual' => 'Igual a (=)',
                                                'mayor_igual' => 'Mayor o igual (≥)',
                                                'menor_igual' => 'Menor o igual (≤)',
                                                'entre' => 'Entre',
                                                'ninguno' => 'No aplica',
                                            ])
                                            ->default('ninguno')
                                            ->native(false),
                                    ]),

                                Textarea::make('descripcion')
                                    ->label('Descripción Detallada')
                                    ->placeholder('Información adicional sobre esta condición...')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Toggle::make('obligatorio')
                                    ->label('¿Es obligatorio?')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Condición')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                            $state['tipo']
                                ? ucfirst($state['tipo']) . ($state['valor'] ? ': ' . $state['valor'] : '')
                                : 'Nueva Condición'
                            )
                            ->columnSpanFull()
                            ->collapsed()
                            ->cloneable(),
                    ]),
            ]);
    }
}
