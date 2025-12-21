<?php

namespace App\Filament\Pages;

use App\Filament\Components\QrCode;
use App\Models\Curso;
use App\Models\Estado;
use App\Models\Estudiante;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Text;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CrearInscripcionAvanzadaIrregular extends Page implements HasForms
{
    protected string $view = 'filament.pages.crear-inscripcion-avanzada-irregular';

    protected static string|null|\UnitEnum $navigationGroup = 'Inscripcion Estudiantil';

    protected static ?string $navigationLabel = 'Inscripción Irregular';

    protected static ?string $title = 'Formulario de Inscripción Irregular';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Estudiante')
                        ->description('Seleccione el estudiante a inscribir')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Section::make('Información del Estudiante')
                                ->description('Busque y seleccione el estudiante que desea inscribir')
                                ->schema([
                                    Select::make('estudiante_id')
                                        ->label('Estudiante')
                                        ->options(function () {
                                            return Estudiante::with('persona')
                                                ->get()
                                                ->mapWithKeys(function ($estudiante) {
                                                    $label = $estudiante->persona
                                                        ? "{$estudiante->persona->nombre} {$estudiante->persona->apellido_pat} {$estudiante->persona->apellido_mat} ({$estudiante->codigo_saga})"
                                                        : $estudiante->codigo_saga;
                                                    return [$estudiante->id => $label];
                                                });
                                        })
                                        ->searchable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state) {
                                                $estudiante = Estudiante::with('persona')->find($state);
                                                if ($estudiante && $estudiante->persona) {
                                                    $set('estudiante_nombre', "{$estudiante->persona->nombre} {$estudiante->persona->apellido_pat} {$estudiante->persona->apellido_mat}");
                                                    $set('estudiante_ci', $estudiante->persona->carnet_identidad ?? 'N/A');
                                                    $set('estudiante_codigo', $estudiante->codigo_saga ?? 'N/A');
                                                    $set('estudiante_estado', $estudiante->estado_academico ?? 'N/A');
                                                }
                                            } else {
                                                $set('estudiante_nombre', null);
                                                $set('estudiante_ci', null);
                                                $set('estudiante_codigo', null);
                                                $set('estudiante_estado', null);
                                            }
                                        })
                                        ->helperText('Busque por nombre o código SAGA')
                                        ->columnSpanFull(),

                                    Section::make('Detalles del Estudiante')
                                        ->icon(Heroicon::OutlinedUser)
                                        ->columnSpanFull()
                                        ->compact()
                                        ->columns(2)
                                        ->schema([
                                            TextInput::make('estudiante_nombre')
                                                ->label('Nombre Completo')
                                                ->default('N/A o requiere recargo')
                                                ->disabled(),

                                            TextInput::make('estudiante_ci')
                                                ->label('Cédula de Identidad')
                                                ->default('N/A o requiere recargo')
                                                ->disabled(),

                                            TextInput::make('estudiante_codigo')
                                                ->label('Código SAGA')
                                                ->default('N/A o requiere recargo')
                                                ->disabled(),

                                            TextInput::make('estudiante_estado')
                                                ->label('Estado Académico')
                                                ->default('N/A o requiere recargo')
                                                ->disabled()
                                        ])
                                ])
                                ->columns(2),
                        ]),

                    Wizard\Step::make('Gestión y Cursos')
                        ->description('Seleccione la gestión y el curso')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Section::make('Gestión Académica')
                                ->description('Configure la gestión y los cursos para la inscripción')
                                ->schema([
                                    Select::make('gestion_id')
                                        ->label('Gestión')
                                        ->options(function () {
                                            return Gestion::query()
                                                ->orderBy('nombre', 'desc')
                                                ->get()
                                                ->mapWithKeys(fn ($gestion) => [
                                                    $gestion->id => "{$gestion->nombre} ({$gestion->fecha_inicio->format('Y-m-d')} - {$gestion->fecha_fin->format('Y-m-d')})"
                                                ]);
                                        })
                                        ->searchable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (callable $set) {
                                            $set('cursos', null);
                                            $set('condiciones', []);
                                        })
                                        ->helperText('Seleccione la gestión académica vigente'),

                                    Select::make('cursos')
                                        ->label('Cursos')
                                        ->options(function (Get $get) {
                                            $gestionId = $get('gestion_id');
                                            if (!$gestionId) {
                                                return [];
                                            }
                                            return Curso::with(['materia', 'turno', 'grupos'])
                                                ->where('gestion_id', $gestionId)
                                                ->where('habilitado', true)
                                                ->get()
                                                ->mapWithKeys(fn ($curso) => [
                                                    $curso->id => "{$curso->materia->nombre} - {$curso->seccion} [{$curso->turno->nombre}] (" .
                                                                ($curso->grupos->isNotEmpty() 
                                                                    ? $curso->grupos->pluck('nombre')->implode(', ') 
                                                                    : 'IRREGULAR') 
                                                                . ")"
                                                ]);
                                        })
                                        ->multiple()
                                        ->searchable()
                                        ->live()
                                        ->afterStateUpdated(function (callable $set, $state) {
                                            if (!$state || empty($state)) {
                                                $set('condiciones', []);
                                                return;
                                            }

                                            // $state es un array de IDs cuando es multiple
                                            $cursos = Curso::with('grupos')->find($state);
                                            
                                            // Colección para almacenar todas las condiciones únicas
                                            $todasLasCondiciones = collect();
                                            
                                            // Iterar sobre cada curso en la colección
                                            foreach ($cursos as $curso) {
                                                if ($curso->grupos && $curso->grupos->isNotEmpty()) {
                                                    foreach ($curso->grupos as $grupo) {
                                                        // Decodificar las condiciones del grupo si están en JSON
                                                        $condicionesGrupo = is_string($grupo->condiciones)
                                                            ? json_decode($grupo->condiciones, true)
                                                            : $grupo->condiciones;
                                                        
                                                        if (is_array($condicionesGrupo) && !empty($condicionesGrupo)) {
                                                            foreach ($condicionesGrupo as $condicion) {
                                                                // Crear una clave única para evitar duplicados
                                                                $claveUnica = md5(json_encode([
                                                                    'tipo' => $condicion['tipo'] ?? '',
                                                                    'valor' => $condicion['valor'] ?? '',
                                                                    'operador' => $condicion['operador'] ?? '',
                                                                ]));
                                                                
                                                                // Solo agregar si no existe ya
                                                                if (!$todasLasCondiciones->has($claveUnica)) {
                                                                    $todasLasCondiciones->put($claveUnica, array_merge($condicion, ['cumple' => false]));
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            // Establecer las condiciones únicas
                                            $set('condiciones', $todasLasCondiciones->values()->toArray());
                                        })
                                        ->disabled(fn (Get $get) => !$get('gestion_id'))
                                        ->helperText(fn (Get $get) =>
                                            $get('gestion_id')
                                                ? 'Seleccione los cursos para la inscripción irregular'
                                                : 'Primero debe seleccionar una gestión'
                                        ),

                                    Placeholder::make('cursos_info')
                                        ->label('Información de los Cursos')
                                        ->content(function (Get $get) {
                                            $cursosIds = $get('cursos');
                                            if (!$cursosIds || empty($cursosIds)) {
                                                return 'Seleccione cursos para ver más información';
                                            }

                                            $cursos = Curso::with(['materia', 'turno', 'grupos'])->find($cursosIds);
                                            if ($cursos->isEmpty()) {
                                                return 'No se encontró información';
                                            }

                                            $info = "📚 Total de cursos seleccionados: " . $cursos->count() . "\n\n";
                                            
                                            foreach ($cursos as $curso) {
                                                $info .= "• {$curso->materia->nombre} ({$curso->seccion}) - {$curso->turno->nombre}\n";
                                                if ($curso->grupos && $curso->grupos->isNotEmpty()) {
                                                    $info .= "  Grupos: " . $curso->grupos->pluck('nombre')->implode(', ') . "\n";
                                                }
                                            }

                                            return $info;
                                        })
                                        ->hidden(fn (Get $get) => !filled($get('cursos')))
                                        ->columnSpanFull(),

                                    Repeater::make('condiciones')
                                        ->label('Condiciones de los Grupos')
                                        ->required()
                                        ->hidden(fn (Get $get) => !filled($get('cursos')))
                                        ->schema([
                                            Select::make('tipo')
                                                ->label('Tipo de Condición')
                                                ->disabled()
                                                ->dehydrated()
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
                                                        ->disabled()
                                                        ->dehydrated()
                                                        ->placeholder('Ej: Mínimo 70%, Mayor a 15 años, etc.')
                                                        ->maxLength(255),

                                                    Select::make('operador')
                                                        ->label('Operador')
                                                        ->disabled()
                                                        ->dehydrated()
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
                                                ->readOnly()
                                                ->columnSpanFull(),

                                            Toggle::make('obligatorio')
                                                ->label('¿Es obligatorio?')
                                                ->default(true)
                                                ->disabled()
                                                ->dehydrated()
                                                ->inline(false),

                                            Toggle::make('cumple')
                                                ->label('¿Cumple?')
                                                ->default(false)
                                                ->inline(false)
                                                ->helperText('Marque si cumple con esta condición'),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->reorderable(false)
                                        ->addable(false)
                                        ->deletable(false)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                                            ]),
                    Wizard\Step::make('Detalles de Inscripción')
                        ->description('Seleccione la gestión y el curso')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Section::make('Información de Inscripción')
                                ->description('Datos administrativos de la inscripción')
                                ->schema([
                                    TextInput::make('codigo_inscripcion')
                                        ->label('Código de Inscripción')
                                        ->default(fn () => 'INS-' . now()->format('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT))
                                        ->required()
                                        ->maxLength(50)
                                        ->helperText('Código único para identificar esta inscripción')
                                        ->readOnly()
                                        ->live()
                                        ->suffixIcon('heroicon-o-hashtag'),

                                    DatePicker::make('fecha_inscripcion')
                                        ->label('Fecha de Inscripción')
                                        ->default(now())
                                        ->required()
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->maxDate(now())
                                        ->suffixIcon('heroicon-o-calendar'),

                                    Select::make('estado_id')
                                        ->label('Estado')
                                        ->searchable()
                                        ->options(fn () => Estado::where('tipo', 'inscripcion')->pluck('nombre', 'id'))
                                        ->default(fn () => Estado::where('nombre', 'LIKE', '%activ%')->first()?->id)
                                        ->required()
                                        ->suffixIcon('heroicon-o-check-badge')
                                        ->helperText('Estado inicial de la inscripción'),

                                    Section::make('Estudiante')
                                        ->columnSpan(2)
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextInput::make('estudiante_nombre')
                                                        ->label('Estudiante')
                                                        ->columns(1)
                                                        ->readOnly()
                                                        ->disabled(),
                                                    TextInput::make('estudiante_codigo')
                                                        ->label('Código SAGA')
                                                        ->columns(1)
                                                        ->readOnly()
                                                        ->disabled(),

                                                    // este atributo es condicionado - si el estudiante ya esta inscrito en el curso seleccionado, entonces mostrar "El estudiante ya está inscrito en este grupo para la gestión seleccionada."
                                                    Placeholder::make('inscripcion_validacion')
                                                        ->label('Validación de Inscripción')
                                                        ->content(function (Get $get) {
                                                            $estudianteId = $get('estudiante_id');
                                                            $grupoId = $get('grupo_id');
                                                            if (!$grupoId) return 'Seleccione un grupo y estudiante para ver más información';

                                                            $grupo = Grupo::with('cursos')->find($grupoId);
                                                            if (!$grupo) return 'No se encontró información';

                                                            $estudiante = Estudiante::with('persona')->find($estudianteId);
                                                            if (!$estudiante) return 'No se encontró información del estudiante';

                                                            $existente = Inscripcion::where('estudiante_id', $estudianteId)
                                                                ->where('grupo_id', $grupoId)
                                                                ->where('gestion_id', $grupo->gestion_id)
                                                                ->first();

                                                            $info = $existente
                                                                ? "⚠️ El estudiante {$estudiante->persona->nombre} {$estudiante->persona->apellido_pat} ya está inscrito en este grupo para la gestión seleccionada y su inscripcion es invalida."
                                                                : "✅ El estudiante {$estudiante->persona->nombre} {$estudiante->persona->apellido_pat} no está inscrito en este grupo y puede proceder con la inscripción.";


                                                            return $info;
                                                        })
                                                        ->hidden(fn (Get $get) => !filled($get('grupo_id')))
                                                        ->columnSpanFull(),


                                                ])
                                        ]),

                                    QrCode::make('codigo_inscripcion')
                                        ->data(fn (Get $get) => $get('codigo_inscripcion'))
                                        ->size(250)
                                        ->alignment('center')
                                        ->visible(fn (Get $get) => filled($get('codigo_inscripcion'))), // Mostrar solo si hay código
                                    Section::make('Información del Curso y Materias')
                                        ->columnSpanFull()
                                        ->schema([
                                            KeyValueEntry::make('materias')
                                                ->label('Lista de materias y profesores')
                                                ->keyLabel("Materia")
                                                ->valueLabel("Profesor")
                                                ->hidden(fn (Get $get) => !filled($get('cursos')))
                                                ->state(function (Get $get) {
                                                    $cursosList = $get('cursos');
                                                    if (!$cursosList) return [];

                                                    $cursos = Curso::with(['materia', 'profesor.persona'])->find($cursosList);
                                                    
                                                    if (!$cursos) return [];

                                                    $materias = [];
                                                    foreach ($cursos as $curso) {
                                                        $nombreMateria = $curso->materia->nombre ?? 'Sin materia';
                                                        $nombreProfesor = $curso->profesor
                                                            ? "{$curso->profesor->persona->nombre} {$curso->profesor->persona->apellido_pat} {$curso->profesor->persona->apellido_mat}"
                                                            : 'Sin profesor';

                                                        $materias[$nombreMateria] = $nombreProfesor;
                                                    }

                                                    return $materias;
                                                })
                                            ,
                                            Text::make('grupo_info')
                                                ->disabled()
                                                ->content(function (Get $get) {
                                                    $grupoId = $get('grupo_id');
                                                    if (!$grupoId) return 'Seleccione un grupo para ver más información';

                                                    $grupo = Grupo::with('cursos')->find($grupoId);
                                                    if (!$grupo) return 'No se encontró información';

                                                    $info = "📋 {$grupo->descripcion}\n\n";

                                                    if ($grupo->cursos && $grupo->cursos->count() > 0) {
                                                        $info .= "📚 Cursos asignados: " . $grupo->cursos->count();
                                                    }

                                                    return $info;
                                                }),
                                        ])
                                ])
                                ->columns(3),
                        ])
                        
                ])
                ->columnSpanFull()
                ->submitAction(view('filament.pages.components.submit-button'))
                ->persistStepInQueryString()
                ->skippable(true),
            ])->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();
        
        // Validar que todas las condiciones obligatorias se cumplan
        /*
        $condiciones = $data['condiciones'] ?? [];
        $condicionesIncumplidas = collect($condiciones)->filter(function ($condicion) {
            return ($condicion['obligatorio'] ?? false) && !($condicion['cumple'] ?? false);
        });

        if ($condicionesIncumplidas->isNotEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Condiciones no cumplidas')
                ->body('El estudiante no cumple con todas las condiciones obligatorias.')
                ->danger()
                ->send();
            return;
        }
        */

        // Aquí va tu lógica para crear las inscripciones
        foreach ($data['cursos'] as $cursoId) {
            Inscripcion::create([
                'estudiante_id' => $data['estudiante_id'],
                'curso_id' => $cursoId,
                'gestion_id' => $data['gestion_id'],
                'tipo' => 'irregular',
                // ... otros campos
            ]);
        }

        \Filament\Notifications\Notification::make()
            ->title('Inscripción creada exitosamente')
            ->success()
            ->send();
    }

    public function getSubheading(): ?string
    {
        return 'Complete el formulario para inscribir a un estudiante de manera irregular a uno o más cursos.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_inscriptions')
                ->label('Ver inscripciones')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(route('filament.informatica.resources.inscripcions.index')),
        ];
    }
}