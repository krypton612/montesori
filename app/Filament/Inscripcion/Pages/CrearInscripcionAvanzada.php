<?php

namespace App\Filament\Inscripcion\Pages;

use App\Filament\Components\QrCode;
use App\Models\Estudiante;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\Estado;
use App\Models\TipoDocumento;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CrearInscripcionAvanzada extends Page implements HasForms
{
    use InteractsWithForms;

    // Si ya creaste una vista separada para panel inscripcion, deja esto:
    protected string $view = 'filament.pages.crear-inscripcion-avanzada';

    protected static string|null|\UnitEnum $navigationGroup = 'Inscripcion Estudiantil';
    protected static ?string $navigationLabel = 'Inscripción Avanzada';
    protected static ?string $title = 'Formulario de Inscripción Avanzada';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function fail(string $title, string $body, array $fieldErrors = []): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->danger()
            ->persistent()
            ->send();

        if (!empty($fieldErrors)) {
            throw ValidationException::withMessages($fieldErrors);
        }

        throw new Halt();
    }

    protected function validateRestricciones(array $data): void
    {
        // 0) mínimos
        if (empty($data['estudiante_id'])) {
            $this->fail('Falta estudiante', 'Seleccione un estudiante para continuar.', [
                'estudiante_id' => 'Debe seleccionar un estudiante.',
            ]);
        }
        if (empty($data['gestion_id'])) {
            $this->fail('Falta gestión', 'Seleccione una gestión para continuar.', [
                'gestion_id' => 'Debe seleccionar una gestión.',
            ]);
        }
        if (empty($data['grupo_id'])) {
            $this->fail('Falta grupo', 'Seleccione un grupo para continuar.', [
                'grupo_id' => 'Debe seleccionar un grupo.',
            ]);
        }

        // 1) estudiante válido + habilitado + con apoderado principal
        $estudiante = Estudiante::with(['persona', 'apoderados'])->find($data['estudiante_id']);
        if (!$estudiante) {
            $this->fail('Estudiante no encontrado', 'El estudiante seleccionado no existe.', [
                'estudiante_id' => 'Seleccione otro estudiante.',
            ]);
        }

        if ($estudiante->persona && $estudiante->persona->habilitado === false) {
            $this->fail('Estudiante inhabilitado', 'El estudiante está marcado como inhabilitado y no puede inscribirse.', [
                'estudiante_id' => 'Estudiante inhabilitado.',
            ]);
        }

        $tieneApoderado = $estudiante->apoderados()->exists();
        $tienePrincipal = $estudiante->apoderados()->wherePivot('es_principal', true)->exists();

        if (!$tieneApoderado || !$tienePrincipal) {
            $this->fail(
                'Faltan apoderados',
                'El estudiante debe tener al menos un apoderado registrado y uno marcado como principal.',
                ['estudiante_id' => 'Debe tener apoderado(s) y uno principal.']
            );
        }

        // 2) gestión válida + habilitada + fecha dentro del rango
        $gestion = Gestion::find($data['gestion_id']);
        if (!$gestion) {
            $this->fail('Gestión no encontrada', 'La gestión seleccionada no existe.', [
                'gestion_id' => 'Seleccione otra gestión.',
            ]);
        }

        if ($gestion->habilitado === false) {
            $this->fail('Gestión no habilitada', 'La gestión seleccionada no está habilitada para inscripciones.', [
                'gestion_id' => 'Gestión no habilitada.',
            ]);
        }

        $fecha = $data['fecha_inscripcion'] ?? null;
        if ($fecha && $gestion->fecha_inicio && $gestion->fecha_fin) {
            $inicio = $gestion->fecha_inicio->format('Y-m-d');
            $fin = $gestion->fecha_fin->format('Y-m-d');

            if ($fecha < $inicio) {
                $this->fail('Fecha inválida', 'La fecha de inscripción es anterior al inicio de la gestión.', [
                    'fecha_inscripcion' => 'Debe estar dentro del rango de la gestión.',
                ]);
            }

            if ($fecha > $fin) {
                $this->fail('Fecha inválida', 'La fecha de inscripción es posterior al fin de la gestión.', [
                    'fecha_inscripcion' => 'Debe estar dentro del rango de la gestión.',
                ]);
            }
        }

        // 3) grupo activo + pertenece a la gestión
        $grupo = Grupo::with(['cursos', 'cursos.materia'])->find($data['grupo_id']);
        if (!$grupo) {
            $this->fail('Grupo no encontrado', 'El grupo seleccionado no existe.', [
                'grupo_id' => 'Seleccione otro grupo.',
            ]);
        }

        if ((bool) $grupo->activo !== true) {
            $this->fail('Grupo inactivo', 'El grupo seleccionado está inactivo.', [
                'grupo_id' => 'Grupo inactivo.',
            ]);
        }

        if ((int) $grupo->gestion_id !== (int) $gestion->id) {
            $this->fail(
                'Grupo no corresponde',
                'El grupo no pertenece a la gestión seleccionada. Vuelva a elegir gestión y grupo.',
                ['grupo_id' => 'Grupo no pertenece a la gestión.']
            );
        }

        // 4) el grupo debe tener cursos
        if ($grupo->cursos->count() === 0) {
            $this->fail('Grupo sin cursos', 'Este grupo no tiene cursos/materias asignadas. No se puede inscribir.', [
                'grupo_id' => 'Grupo sin cursos.',
            ]);
        }

        // 5) cupos: ningún curso puede estar lleno
        $cursosLlenos = $grupo->cursos->filter(function ($curso) {
            if ($curso->cupo_maximo === null) return false;
            return (int) $curso->cupo_actual >= (int) $curso->cupo_maximo;
        });

        if ($cursosLlenos->isNotEmpty()) {
            $lista = $cursosLlenos
                ->map(fn ($c) => ($c->materia?->nombre ?? 'Curso') . " (cupo {$c->cupo_actual}/{$c->cupo_maximo})")
                ->values()
                ->implode(', ');

            $this->fail(
                'Sin cupos disponibles',
                "No se puede inscribir porque hay cursos del grupo sin cupo: {$lista}.",
                ['grupo_id' => 'No hay cupo en uno o más cursos del grupo.']
            );
        }

        // 6) condiciones obligatorias: deben estar cumple=true
        $condiciones = collect($data['condiciones'] ?? []);
        $pendientes = $condiciones->filter(fn ($c) => !empty($c['obligatorio']) && empty($c['cumple']));

        if ($pendientes->isNotEmpty()) {
            $detalle = $pendientes
                ->map(fn ($c) => ($c['tipo'] ?? 'condición') . ': ' . ($c['valor'] ?? ''))
                ->values()
                ->implode(' | ');

            $this->fail(
                'Condiciones no cumplidas',
                "Marque como cumplidas las condiciones obligatorias antes de continuar. Pendientes: {$detalle}",
                ['condiciones' => 'Hay condiciones obligatorias sin cumplir.']
            );
        }

        // 7) documentos: obligatorio al menos 1 (panel inscripcion)
        $docs = collect($data['documentos'] ?? []);
        if ($docs->isEmpty()) {
            $this->fail(
                'Faltan documentos',
                'Debe adjuntar al menos un documento para registrar la inscripción.',
                ['documentos' => 'Adjunte al menos un documento.']
            );
        }
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
                                                ->default('N/A')
                                                ->disabled(),

                                            TextInput::make('estudiante_ci')
                                                ->label('Cédula de Identidad')
                                                ->default('N/A')
                                                ->disabled(),

                                            TextInput::make('estudiante_codigo')
                                                ->label('Código SAGA')
                                                ->default('N/A')
                                                ->disabled(),

                                            TextInput::make('estudiante_estado')
                                                ->label('Estado Académico')
                                                ->default('N/A')
                                                ->disabled(),
                                        ]),
                                ])
                                ->columns(2),
                        ]),

                    Wizard\Step::make('Gestión y Grupo')
                        ->description('Seleccione la gestión y el grupo')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Section::make('Gestión Académica')
                                ->description('Configure la gestión y el grupo para la inscripción')
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
                                        ->afterStateUpdated(fn (callable $set) => $set('grupo_id', null))
                                        ->helperText('Seleccione la gestión académica vigente'),

                                    Select::make('grupo_id')
                                        ->label('Grupo')
                                        ->options(function (Get $get) {
                                            $gestionId = $get('gestion_id');
                                            if (!$gestionId) {
                                                return [];
                                            }
                                            return Grupo::where('gestion_id', $gestionId)
                                                ->where('activo', true)
                                                ->get()
                                                ->mapWithKeys(fn ($grupo) => [
                                                    $grupo->id => "{$grupo->codigo} - {$grupo->nombre}"
                                                ]);
                                        })
                                        ->searchable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (callable $set, $state) {
                                            if ($state) {
                                                $grupo = Grupo::find($state);

                                                if ($grupo && !empty($grupo->condiciones)) {
                                                    $condicionesGrupo = is_string($grupo->condiciones)
                                                        ? json_decode($grupo->condiciones, true)
                                                        : $grupo->condiciones;

                                                    if (is_array($condicionesGrupo)) {
                                                        $condicionesConCumple = array_map(function ($condicion) {
                                                            return array_merge($condicion, ['cumple' => false]);
                                                        }, $condicionesGrupo);

                                                        $set('condiciones', $condicionesConCumple);
                                                    }
                                                } else {
                                                    $set('condiciones', []);
                                                }
                                            }
                                        })
                                        ->disabled(fn (Get $get) => !$get('gestion_id'))
                                        ->helperText(fn (Get $get) => $get('gestion_id')
                                            ? 'Seleccione el grupo al que se inscribirá'
                                            : 'Primero debe seleccionar una gestión'
                                        ),

                                    Placeholder::make('grupo_info')
                                        ->label('Información del Grupo')
                                        ->content(function (Get $get) {
                                            $grupoId = $get('grupo_id');
                                            if (!$grupoId) return 'Seleccione un grupo para ver más información';

                                            $grupo = Grupo::with('cursos')->find($grupoId);
                                            if (!$grupo) return 'No se encontró información';

                                            $info = "📋 {$grupo->descripcion}\n\n";

                                            if ($grupo->cursos && $grupo->cursos->count() > 0) {
                                                $info .= "📚 Materias asignadas: " . $grupo->cursos->count();
                                            }

                                            return $info;
                                        })
                                        ->hidden(fn (Get $get) => !filled($get('grupo_id')))
                                        ->columnSpanFull(),

                                    Repeater::make('condiciones')
                                        ->label('Condiciones')
                                        ->required()
                                        ->hidden(fn (Get $get) => !filled($get('grupo_id')))
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
                                                ->native(false),

                                            Grid::make(2)
                                                ->schema([
                                                    TextInput::make('valor')
                                                        ->label('Valor/Descripción')
                                                        ->required()
                                                        ->disabled()
                                                        ->dehydrated()
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
                                        ->reorderable()
                                        ->addable(false)
                                        ->deletable(false)
                                        ->columnSpanFull(),
                                ])
                                ->columns(2),
                        ]),

                    Wizard\Step::make('Detalles de Inscripción')
                        ->description('Complete los datos de la inscripción')
                        ->icon('heroicon-o-document-text')
                        ->beforeValidation(function (Get $get) {
                            $data = [
                                'estudiante_id'     => $get('estudiante_id'),
                                'gestion_id'        => $get('gestion_id'),
                                'grupo_id'          => $get('grupo_id'),
                                'fecha_inscripcion' => $get('fecha_inscripcion'),
                                'condiciones'       => $get('condiciones'),
                                'documentos'        => $get('documentos'),
                            ];

                            // NUEVAS RESTRICCIONES
                            $this->validateRestricciones($data);

                            // Duplicado
                            $existente = Inscripcion::where('estudiante_id', $data['estudiante_id'])
                                ->where('grupo_id', $data['grupo_id'])
                                ->where('gestion_id', $data['gestion_id'])
                                ->first();

                            if ($existente) {
                                $this->fail(
                                    'Inscripción duplicada',
                                    'El estudiante ya está inscrito en este grupo para la gestión seleccionada.',
                                    ['grupo_id' => 'Ya existe una inscripción para este estudiante en este grupo y gestión.']
                                );
                            }
                        })
                        ->schema([
                            Section::make('Información de Inscripción')
                                ->description('Datos administrativos de la inscripción')
                                ->schema([
                                    TextInput::make('codigo_inscripcion')
                                        ->label('Código de Inscripción')
                                        ->default(fn () => 'INS-' . now()->format('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT))
                                        ->required()
                                        ->unique(Inscripcion::class, 'codigo_inscripcion')
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
                                        ->options(fn () => Estado::whereRaw("upper(tipo) = 'INSCRIPCION'")->pluck('nombre', 'id'))
                                        ->default(fn () => Estado::whereRaw("upper(tipo) = 'INSCRIPCION'")->where('nombre', 'Inscrito')->value('id'))
                                        ->required()
                                        ->suffixIcon('heroicon-o-check-badge')
                                        ->helperText('Estado inicial de la inscripción'),

                                    Section::make('Estudiante')
                                        ->columnSpan(2)
                                        ->schema([
                                            Grid::make(2)->schema([
                                                TextInput::make('estudiante_nombre')
                                                    ->label('Estudiante')
                                                    ->readOnly()
                                                    ->disabled(),

                                                TextInput::make('estudiante_codigo')
                                                    ->label('Código SAGA')
                                                    ->readOnly()
                                                    ->disabled(),

                                                Placeholder::make('inscripcion_validacion')
                                                    ->label('Validación de Inscripción')
                                                    ->content(function (Get $get) {
                                                        $estudianteId = $get('estudiante_id');
                                                        $grupoId = $get('grupo_id');
                                                        $gestionId = $get('gestion_id');

                                                        if (!$grupoId || !$estudianteId || !$gestionId) {
                                                            return 'Seleccione un grupo, estudiante y gestión para ver la validación.';
                                                        }

                                                        $grupo = Grupo::find($grupoId);
                                                        $estudiante = Estudiante::with('persona')->find($estudianteId);

                                                        if (!$grupo || !$estudiante || !$estudiante->persona) {
                                                            return 'No se encontró información.';
                                                        }

                                                        $existente = Inscripcion::where('estudiante_id', $estudianteId)
                                                            ->where('grupo_id', $grupoId)
                                                            ->where('gestion_id', $gestionId)
                                                            ->first();

                                                        return $existente
                                                            ? "⚠️ El estudiante {$estudiante->persona->nombre} {$estudiante->persona->apellido_pat} ya está inscrito en este grupo para la gestión seleccionada."
                                                            : "✅ El estudiante {$estudiante->persona->nombre} {$estudiante->persona->apellido_pat} puede proceder con la inscripción.";
                                                    })
                                                    ->hidden(fn (Get $get) => !filled($get('grupo_id')))
                                                    ->columnSpanFull(),
                                            ]),
                                        ]),

                                    QrCode::make('codigo_inscripcion')
                                        ->data(fn (Get $get) => $get('codigo_inscripcion'))
                                        ->size(250)
                                        ->alignment('center')
                                        ->visible(fn (Get $get) => filled($get('codigo_inscripcion'))),

                                    Section::make('Información del Curso y Materias')
                                        ->columnSpanFull()
                                        ->schema([
                                            \Filament\Infolists\Components\KeyValueEntry::make('materias')
                                                ->label('Lista de materias y profesores')
                                                ->keyLabel("Materia")
                                                ->valueLabel("Profesor")
                                                ->hidden(fn (Get $get) => !filled($get('grupo_id')))
                                                ->state(function (Get $get) {
                                                    $grupoId = $get('grupo_id');
                                                    if (!$grupoId) return [];

                                                    $grupo = Grupo::with(['cursos.materia', 'cursos.profesor.persona'])->find($grupoId);
                                                    if (!$grupo || !$grupo->cursos) return [];

                                                    $materias = [];
                                                    foreach ($grupo->cursos as $curso) {
                                                        $nombreMateria = $curso->materia->nombre ?? 'Sin materia';
                                                        $nombreProfesor = $curso->profesor
                                                            ? "{$curso->profesor->persona->nombre} {$curso->profesor->persona->apellido_pat} {$curso->profesor->persona->apellido_mat}"
                                                            : 'Sin profesor';

                                                        $materias[$nombreMateria] = $nombreProfesor;
                                                    }

                                                    return $materias;
                                                }),

                                            Text::make('grupo_info')
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
                                        ]),
                                ])
                                ->columns(3),
                        ]),

                    Wizard\Step::make('Documentos')
                        ->description('Adjunte los documentos requeridos')
                        ->icon('heroicon-o-paper-clip')
                        ->schema([
                            Section::make('Documentos de Inscripción')
                                ->description('Adjunte todos los documentos necesarios para la inscripción (obligatorio)')
                                ->schema([
                                    Repeater::make('documentos')
                                        ->label('Documentos')
                                        ->minItems(1)
                                        ->required()
                                        ->schema([
                                            Select::make('tipo_documento_id')
                                                ->label('Tipo de Documento')
                                                ->options(fn () => TipoDocumento::whereRaw("upper(tipo) = 'INSCRIPCION'")->pluck('nombre', 'id'))
                                                ->required()
                                                ->searchable()
                                                ->live()
                                                ->columnSpan(1),

                                            FileUpload::make('nombre_archivo')
                                                ->label('Archivo')
                                                ->directory('inscripciones/documentos')
                                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                                ->maxSize(5120)
                                                ->required()
                                                ->downloadable()
                                                ->previewable()
                                                ->columnSpan(1),
                                        ])
                                        ->columns(2)
                                        ->addActionLabel('Agregar documento')
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string =>
                                            TipoDocumento::find($state['tipo_documento_id'] ?? null)?->nombre ?? 'Nuevo documento'
                                        )
                                        ->defaultItems(1)
                                        ->columnSpanFull()
                                        ->deleteAction(fn (Action $action) => $action->requiresConfirmation()),
                                ])
                                ->icon('heroicon-o-document-duplicate'),
                        ]),
                ])
                    ->columnSpanFull()
                    ->submitAction(view('filament.pages.components.submit-button'))
                    ->persistStepInQueryString()
                    ->skippable(false),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        try {
            $data = $this->form->getState();

            // Re-validación final (seguridad)
            $this->validateRestricciones($data);

            // Validar duplicado (seguridad)
            $existente = Inscripcion::where('estudiante_id', $data['estudiante_id'])
                ->where('grupo_id', $data['grupo_id'])
                ->where('gestion_id', $data['gestion_id'])
                ->first();

            if ($existente) {
                $this->fail(
                    'Inscripción duplicada',
                    'El estudiante ya está inscrito en este grupo para la gestión seleccionada.',
                    ['grupo_id' => 'Ya existe una inscripción para este estudiante en este grupo y gestión.']
                );
            }

            DB::beginTransaction();

            $inscripcion = Inscripcion::create([
                'codigo_inscripcion' => $data['codigo_inscripcion'],
                'estudiante_id' => $data['estudiante_id'],
                'grupo_id' => $data['grupo_id'],
                'gestion_id' => $data['gestion_id'],
                'fecha_inscripcion' => $data['fecha_inscripcion'],
                'estado_id' => $data['estado_id'],
                'condiciones' => $data['condiciones'] ?? [],
            ]);

            // Documentos
            if (!empty($data['documentos'])) {
                foreach ($data['documentos'] as $documento) {
                    $inscripcion->documentos()->create([
                        'tipo_documento_id' => $documento['tipo_documento_id'],
                        'nombre_archivo' => $documento['nombre_archivo'],
                    ]);
                }
            }

            // Subir cupo_actual de cursos del grupo
            $grupo = Grupo::with('cursos')->find($data['grupo_id']);
            foreach ($grupo->cursos as $curso) {
                $curso->increment('cupo_actual');
            }

            // (Opcional) marcar estudiante como inscrito
            Estudiante::where('id', $data['estudiante_id'])
                ->update(['estado_academico' => 'inscrito']);

            DB::commit();

            Notification::make()
                ->title('¡Inscripción creada exitosamente!')
                ->body("La inscripción {$data['codigo_inscripcion']} ha sido registrada correctamente.")
                ->success()
                ->seconds(5)
                ->send();

            // OJO: panel inscripcion
            $this->redirect(route('filament.inscripcion.resources.inscripcions.index'));
        } catch (Halt $exception) {
            return;
        } catch (ValidationException $e) {
            // Ya muestra mensajes en UI
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error al crear la inscripción')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function getSubheading(): ?string
    {
        return 'Complete el formulario para inscribir a un estudiante en un grupo específico para la gestión académica seleccionada.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_inscriptions')
                ->label('Ver inscripciones')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(route('filament.inscripcion.resources.inscripcions.index')),
        ];
    }

    public function getFooter(): ?View
    {
        // Si quieres footer separado para panel inscripcion, crea otra blade y cambia aquí:
        return view('filament.pages.footer-inscripcion-avanzada');
    }
}
