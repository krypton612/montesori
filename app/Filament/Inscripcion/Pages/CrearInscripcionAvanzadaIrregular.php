<?php

namespace App\Filament\Inscripcion\Pages;

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
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrearInscripcionAvanzadaIrregular extends Page implements HasForms
{

    use InteractsWithForms;

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
                    $this->getEstudianteStep(),
                    $this->getGestionCursosStep(),
                    $this->getDetallesInscripcionStep(),
                ])
                ->columnSpanFull()
                ->submitAction(view('filament.pages.components.submit-button'))
                ->persistStepInQueryString()
                ->skippable(false),
            ])
            ->statePath('data');
    }

    protected function getEstudianteStep(): Wizard\Step
    {
        return Wizard\Step::make('Estudiante')
            ->description('Seleccione el estudiante a inscribir')
            ->icon('heroicon-o-user')
            ->schema([
                Section::make('Información del Estudiante')
                    ->description('Busque y seleccione el estudiante que desea inscribir')
                    ->schema([
                        Select::make('estudiante_id')
                            ->label('Estudiante')
                            ->options($this->getEstudiantesOptions())
                            ->searchable()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $this->actualizarDatosEstudiante($state, $set))
                            ->helperText('Busque por nombre, apellido o código SAGA')
                            ->columnSpanFull()
                            ->preload(),

                        $this->getDetallesEstudianteSection(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getGestionCursosStep(): Wizard\Step
    {
        return Wizard\Step::make('Gestión y Cursos')
            ->description('Seleccione la gestión y los cursos')
            ->icon('heroicon-o-calendar')
            ->schema([
                Section::make('Gestión Académica')
                    ->description('Configure la gestión y los cursos para la inscripción irregular')
                    ->schema([
                        Select::make('gestion_id')
                            ->label('Gestión Académica')
                            ->options($this->getGestionesOptions())
                            ->searchable()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set) {
                                $set('cursos', null);
                                $set('condiciones', []);
                            })
                            ->helperText('Seleccione la gestión académica vigente')
                            ->preload(),

                        Select::make('cursos')
                            ->label('Cursos')
                            ->options(fn(Get $get) => $this->getCursosOptions($get('gestion_id')))
                            ->multiple()
                            ->searchable()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(callable $set, $state) => $this->procesarCondicionesCursos($set, $state))
                            ->disabled(fn(Get $get) => !$get('gestion_id'))
                            ->helperText(fn(Get $get) => $this->getCursosHelperText($get('gestion_id')))
                            ->preload(),

                        $this->getCursosPreviewRepeater(),

                        $this->getCondicionesRepeater(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getDetallesInscripcionStep(): Wizard\Step
    {
        return Wizard\Step::make('Detalles de Inscripción')
            ->description('Complete los datos administrativos')
            ->icon('heroicon-o-document-check')
            ->schema([
                Section::make('Información Administrativa')
                    ->description('Datos administrativos de la inscripción irregular')
                    ->schema([
                        TextInput::make('codigo_inscripcion')
                            ->label('Código de Inscripción')
                            ->default(fn() => $this->generarCodigoInscripcion())
                            ->required()
                            ->readOnly()
                            ->maxLength(50)
                            ->helperText('Código único auto-generado')
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
                            ->label('Estado de Inscripción')
                            ->searchable()
                            ->options(fn() => Estado::where('tipo', 'inscripcion')->pluck('nombre', 'id'))
                            ->default(fn() => $this->getEstadoDefaultId())
                            ->required()
                            ->suffixIcon('heroicon-o-check-badge')
                            ->helperText('Estado inicial de la inscripción')
                            ->preload(),
                    ])
                    ->columns(3),

                Section::make('Resumen de Inscripción')
                    ->description('Verifique la información antes de confirmar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('estudiante_nombre')
                                    ->label('Estudiante')
                                    ->readOnly()
                                    ->disabled(),

                                TextInput::make('estudiante_codigo')
                                    ->label('Código SAGA')
                                    ->readOnly()
                                    ->disabled(),
                            ]),

                        $this->getValidacionInscripcionPlaceholder(),

                        KeyValueEntry::make('materias_resumen')
                            ->label('Materias y Docentes Asignados')
                            ->keyLabel('Materia (Sección)')
                            ->valueLabel('Docente')
                            ->state(fn(Get $get) => $this->getMateriasProfesores($get('cursos'))),

                        QrCode::make('codigo_inscripcion')
                            ->data(fn(Get $get) => $get('codigo_inscripcion'))
                            ->size(200)
                            ->alignment('center')
                            ->visible(fn(Get $get) => filled($get('codigo_inscripcion'))),
                    ])
                    ->columns(1)
                    ->collapsed(false),
            ]);
    }

    // ====================== MÉTODOS AUXILIARES ======================

    protected function getEstudiantesOptions(): array
    {
        return Estudiante::with('persona')
            ->whereHas('persona')
            ->get()
            ->mapWithKeys(function ($estudiante) {
                $persona = $estudiante->persona;
                $label = sprintf(
                    "%s %s %s (%s)",
                    $persona->nombre,
                    $persona->apellido_pat,
                    $persona->apellido_mat,
                    $estudiante->codigo_saga
                );
                return [$estudiante->id => $label];
            })
            ->toArray();
    }

    protected function actualizarDatosEstudiante(?int $state, callable $set): void
    {
        if (!$state) {
            $this->limpiarDatosEstudiante($set);
            return;
        }

        $estudiante = Estudiante::with('persona')->find($state);

        if (!$estudiante || !$estudiante->persona) {
            $this->limpiarDatosEstudiante($set);
            return;
        }

        $persona = $estudiante->persona;

        $set('estudiante_nombre', sprintf(
            "%s %s %s",
            $persona->nombre,
            $persona->apellido_pat,
            $persona->apellido_mat
        ));
        $set('estudiante_ci', $persona->carnet_identidad ?? 'N/A');
        $set('estudiante_codigo', $estudiante->codigo_saga ?? 'N/A');
        $set('estudiante_estado', $estudiante->estado_academico ?? 'N/A');
    }

    protected function limpiarDatosEstudiante(callable $set): void
    {
        $set('estudiante_nombre', null);
        $set('estudiante_ci', null);
        $set('estudiante_codigo', null);
        $set('estudiante_estado', null);
    }

    protected function getDetallesEstudianteSection(): Section
    {
        return Section::make('Detalles del Estudiante')
            ->icon('heroicon-o-user-circle')
            ->columnSpanFull()
            ->compact()
            ->columns(2)
            ->schema([
                TextInput::make('estudiante_nombre')
                    ->label('Nombre Completo')
                    ->default('Seleccione un estudiante')
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
            ]);
    }

    protected function getGestionesOptions(): array
    {
        return Gestion::query()
            ->orderBy('nombre', 'desc')
            ->get()
            ->mapWithKeys(fn($gestion) => [
                $gestion->id => sprintf(
                    "%s (%s - %s)",
                    $gestion->nombre,
                    $gestion->fecha_inicio->format('d/m/Y'),
                    $gestion->fecha_fin->format('d/m/Y')
                )
            ])
            ->toArray();
    }

    protected function getCursosOptions(?int $gestionId): array
    {
        if (!$gestionId) {
            return [];
        }

        return Curso::with(['materia', 'turno', 'grupos'])
            ->where('gestion_id', $gestionId)
            ->where('habilitado', true)
            ->get()
            ->mapWithKeys(function ($curso) {
                $gruposNombre = $curso->grupos->isNotEmpty()
                    ? $curso->grupos->pluck('nombre')->implode(', ')
                    : 'IRREGULAR';

                $label = sprintf(
                    "%s - %s [%s] (%s)",
                    $curso->materia->nombre,
                    $curso->seccion,
                    $curso->turno->nombre,
                    $gruposNombre
                );

                return [$curso->id => $label];
            })
            ->toArray();
    }

    protected function getCursosHelperText(?int $gestionId): string
    {
        return $gestionId
            ? 'Seleccione uno o más cursos para la inscripción irregular'
            : 'Primero debe seleccionar una gestión académica';
    }

protected function procesarCondicionesCursos(callable $set, $state): void
{
    if (empty($state)) {
        $set('condiciones', []);
        $set('cursos_preview', []);
        return;
    }

    $cursos = Curso::with(['materia', 'turno', 'grupos'])->find($state);

    $preview = $cursos->map(function ($curso) {
        return [
            'materia' => $curso->materia->nombre,
            'seccion' => $curso->seccion,
            'turno'   => $curso->turno->nombre,
            'grupos'  => $curso->grupos->isNotEmpty()
                ? $curso->grupos->pluck('nombre')->implode(', ')
                : 'Inscripción irregular',
        ];
    })->toArray();

    $set('cursos_preview', $preview);

        $condicionesUnicas = collect();

        foreach ($cursos as $curso) {
            if (!$curso->grupos || $curso->grupos->isEmpty()) {
                continue;
            }

            foreach ($curso->grupos as $grupo) {
                $condicionesGrupo = $this->decodificarCondiciones($grupo->condiciones);

                if (!empty($condicionesGrupo)) {
                    foreach ($condicionesGrupo as $condicion) {
                        $claveUnica = $this->generarClaveCondicion($condicion);

                        if (!$condicionesUnicas->has($claveUnica)) {
                            $condicionesUnicas->put(
                                $claveUnica,
                                array_merge($condicion, ['cumple' => false])
                            );
                        }
                    }
                }
            }
        }

        $set('condiciones', $condicionesUnicas->values()->toArray());
    }

    protected function decodificarCondiciones($condiciones): array
    {
        if (is_string($condiciones)) {
            $decoded = json_decode($condiciones, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($condiciones) ? $condiciones : [];
    }

    protected function generarClaveCondicion(array $condicion): string
    {
        return md5(json_encode([
            'tipo' => $condicion['tipo'] ?? '',
            'valor' => $condicion['valor'] ?? '',
            'operador' => $condicion['operador'] ?? '',
        ]));
    }



protected function getCursosPreviewRepeater(): Repeater
{
    return Repeater::make('cursos_preview')
        ->label('Cursos Seleccionados')
        ->schema([
            TextInput::make('materia')
                ->label('Materia')
                ->disabled(),

            TextInput::make('seccion')
                ->label('Sección')
                ->disabled(),

            TextInput::make('turno')
                ->label('Turno')
                ->disabled(),

            TextInput::make('grupos')
                ->label('Grupos')
                ->disabled(),
        ])
        ->columns(4)
        ->disabled()
        ->dehydrated(false)
        ->hidden(fn (Get $get) => !filled($get('cursos')))
        ->columnSpanFull();
}

    protected function getCondicionesRepeater(): Repeater
    {
        return Repeater::make('condiciones')
            ->label('Condiciones de los Grupos')
            ->hidden(fn(Get $get) => !filled($get('cursos')))
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

                Grid::make(2)->schema([
                    TextInput::make('valor')
                        ->label('Valor Requerido')
                        ->required()
                        ->disabled()
                        ->dehydrated()
                        ->placeholder('Ej: 70%, 15 años, etc.')
                        ->maxLength(255),

                    Select::make('operador')
                        ->label('Operador de Comparación')
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
                    ->label('Descripción Adicional')
                    ->placeholder('Detalles o aclaraciones sobre esta condición...')
                    ->rows(2)
                    ->readOnly()
                    ->columnSpanFull(),

                Grid::make(2)->schema([
                    Toggle::make('obligatorio')
                        ->label('¿Es obligatorio cumplir?')
                        ->default(true)
                        ->disabled()
                        ->dehydrated()
                        ->inline(false),

                    Toggle::make('cumple')
                        ->label('✓ El estudiante cumple')
                        ->default(false)
                        ->inline(false)
                        ->helperText('Active si el estudiante cumple esta condición'),
                ]),
            ])
            ->columns(2)
            ->defaultItems(0)
            ->reorderable(false)
            ->addable(false)
            ->deletable(false)
            ->columnSpanFull();
    }

    protected function getValidacionInscripcionPlaceholder(): Placeholder
    {
        return Placeholder::make('validacion_inscripcion')
            ->label('Validación de Inscripción')
            ->content(fn(Get $get) => $this->validarInscripcionExistente(
                $get('estudiante_id'),
                $get('cursos'),
                $get('gestion_id')
            ))
            ->hidden(fn(Get $get) => !filled($get('cursos')) || !filled($get('estudiante_id')))
            ->columnSpanFull();
    }

    protected function validarInscripcionExistente(?int $estudianteId, ?array $cursosIds, ?int $gestionId): string
    {
        if (!$estudianteId || !$cursosIds || !$gestionId) {
            return '⏳ Seleccione estudiante y cursos para validar';
        }

        $estudiante = Estudiante::with('persona')->find($estudianteId);
        if (!$estudiante) {
            return '⚠️ Estudiante no encontrado';
        }

        $nombreCompleto = sprintf(
            "%s %s",
            $estudiante->persona->nombre,
            $estudiante->persona->apellido_pat
        );

        // Verificar inscripciones existentes
        $inscripcionesExistentes = Inscripcion::where('estudiante_id', $estudianteId)
            ->where('gestion_id', $gestionId)
            ->whereIn('curso_id', $cursosIds)
            ->with('curso.materia')
            ->get();

        if ($inscripcionesExistentes->isEmpty()) {
            return "✅ **Validación exitosa:** {$nombreCompleto} no tiene inscripciones previas en estos cursos para la gestión seleccionada.";
        }

        $materias = $inscripcionesExistentes
            ->pluck('curso.materia.nombre')
            ->implode(', ');

        return "⚠️ **Advertencia:** {$nombreCompleto} ya está inscrito en: {$materias}. Verifique antes de continuar.";
    }

    protected function getMateriasProfesores(?array $cursosIds): array
    {
        if (!$cursosIds) {
            return [];
        }

        $cursos = Curso::with(['materia', 'profesor.persona'])->find($cursosIds);

        return $cursos->mapWithKeys(function ($curso) {
            $nombreMateria = sprintf(
                "%s (%s)",
                $curso->materia->nombre ?? 'Sin materia',
                $curso->seccion ?? 'S/N'
            );

            $nombreProfesor = $curso->profesor && $curso->profesor->persona
                ? sprintf(
                    "%s %s %s",
                    $curso->profesor->persona->nombre,
                    $curso->profesor->persona->apellido_pat,
                    $curso->profesor->persona->apellido_mat
                )
                : 'Sin asignar';

            return [$nombreMateria => $nombreProfesor];
        })->toArray();
    }

    protected function generarCodigoInscripcion(): string
    {
        $anio = now()->year;
        $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $uuid = Str::upper(Str::random(4));

        return "INS-IRR-{$anio}-{$random}-{$uuid}";
    }

    protected function getEstadoDefaultId(): ?int
    {
        return Estado::where('tipo', 'inscripcion')
            ->where(function ($query) {
                $query->where('nombre', 'LIKE', '%activ%')
                    ->orWhere('nombre', 'LIKE', '%pendiente%');
            })
            ->first()
            ?->id;
    }

    // ====================== MÉTODO DE CREACIÓN ======================

    public function create(): void
    {
        try {
            $data = $this->form->getState();

            // Validar que no existan inscripciones duplicadas
            $duplicados = $this->verificarInscripcionesDuplicadas(
                $data['estudiante_id'],
                $data['cursos'],
                $data['gestion_id']
            );

            $gestion = Gestion::find($data['gestion_id']);

            $fechaInscripcion = \Illuminate\Support\Carbon::parse($data['fecha_inscripcion'])->startOfDay();
            $inicioGestion    = $gestion->fecha_inicio->startOfDay();
            $finGestion       = $gestion->fecha_fin->endOfDay();

            if (! $fechaInscripcion->between($inicioGestion, $finGestion)) {
                throw new \Exception('Debe estar dentro del rango de la gestión.');
            }


            if ($duplicados->isNotEmpty()) {
                $materias = $duplicados->pluck('curso.materia.nombre')->implode(', ');

                Notification::make()
                    ->title('Inscripción duplicada')
                    ->body("El estudiante ya está inscrito en: {$materias}")
                    ->warning()
                    ->duration(5000)
                    ->send();

                throw new Halt();
            }

            // Crear inscripciones en transacción
            DB::transaction(function () use ($data) {
                $inscripcionesCreadas = 0;

                foreach ($data['cursos'] as $cursoId) {
                    Inscripcion::create([
                        'estudiante_id' => $data['estudiante_id'],
                        'curso_id' => $cursoId,
                        'gestion_id' => $data['gestion_id'],
                        'grupo_id' => null, // Inscripción irregular no tiene grupo
                        'estado_id' => $data['estado_id'],
                        'fecha_inscripcion' => $data['fecha_inscripcion'],
                        'codigo_inscripcion' => $data['codigo_inscripcion'],
                        'tipo' => 'irregular',
                        'condiciones_cumplidas' => json_encode($data['condiciones'] ?? []),
                        'observaciones' => 'Inscripción irregular - Sin grupo asignado',
                    ]);

                    $inscripcionesCreadas++;
                }

                Notification::make()
                    ->title('¡Inscripción exitosa!')
                    ->body("Se crearon {$inscripcionesCreadas} inscripción(es) irregular(es) correctamente.")
                    ->success()
                    ->duration(5000)
                    ->send();
            });

            // Limpiar formulario y redirigir
            $this->form->fill();
            $this->redirect(route('filament.inscripcion.resources.inscripcions.index'));

        } catch (Halt $e) {
            // El halt se maneja automáticamente
            return;
        } catch (\Exception $e) {
            $this->fail('Fecha inválida',
            $e->getMessage(),
            ['fecha_inscripcion' => 'Fecha inválida.',]
            );

            Notification::make()
                ->title('Error al crear inscripción')
                ->body("Ocurrió un error: {$e->getMessage()}")
                ->danger()
                ->duration(8000)
                ->send();

            logger()->error('Error en inscripción irregular', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected function validarCondicionesObligatorias(array $condiciones): bool
    {
        if (empty($condiciones)) {
            return true; // Si no hay condiciones, la validación pasa
        }

        $condicionesIncumplidas = collect($condiciones)->filter(function ($condicion) {
            return ($condicion['obligatorio'] ?? false) && !($condicion['cumple'] ?? false);
        });

        return $condicionesIncumplidas->isEmpty();
    }

    protected function verificarInscripcionesDuplicadas(int $estudianteId, array $cursosIds, int $gestionId)
    {
        return Inscripcion::where('estudiante_id', $estudianteId)
            ->where('gestion_id', $gestionId)
            ->whereIn('curso_id', $cursosIds)
            ->with('curso.materia')
            ->get();
    }

    // ====================== CONFIGURACIÓN DE PÁGINA ======================

    public function getSubheading(): ?string
    {
        return 'Complete el formulario para inscribir a un estudiante de manera irregular a uno o más cursos sin asignación de grupo.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ver_inscripciones')
                ->label('Ver inscripciones')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(route('filament.inscripcion.resources.inscripcions.index')),

            Action::make('ayuda')
                ->label('Ayuda')
                ->icon('heroicon-o-question-mark-circle')
                ->color('info')
                ->modalHeading('Guía de Inscripción Irregular')
                ->modalDescription('La inscripción irregular permite inscribir estudiantes a cursos sin asignarles a un grupo específico. Esto es útil para casos especiales o excepcionales.')
                ->modalWidth('2xl')
                ->modalCancelActionLabel('Cerrar'),
        ];
    }
}
