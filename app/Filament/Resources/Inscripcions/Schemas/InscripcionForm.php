<?php

namespace App\Filament\Resources\Inscripcions\Schemas;

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
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class InscripcionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Estudiante')
                    ->description('Seleccione el estudiante a inscribir')
                    ->icon('heroicon-o-user')
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
                            ->afterStateHydrated(function ($state, callable $set, $context) {
                                // Se ejecuta cuando se carga el formulario en modo edit
                                if ($state && $context === 'edit' || $context === 'view') {

                                    $estudiante = Estudiante::with('persona')->find($state);
                                    if ($estudiante && $estudiante->persona) {
                                        $set('estudiante_nombre', "{$estudiante->persona->nombre} {$estudiante->persona->apellido_pat} {$estudiante->persona->apellido_mat}");
                                        $set('estudiante_ci', $estudiante->persona->ci ?? 'N/A');
                                        $set('estudiante_codigo', $estudiante->codigo_saga ?? 'N/A');
                                        $set('estudiante_estado', $estudiante->estado_academico ?? 'N/A');
                                    }
                                }
                            })
                            ->helperText('Busque por nombre o código SAGA')
                            ->disabled(fn (string $context): bool => $context === 'edit')
                            ->dehydrated()
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
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('estudiante_ci')
                                    ->label('Cédula de Identidad')
                                    ->default('N/A o requiere recargo')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('estudiante_codigo')
                                    ->label('Código SAGA')
                                    ->default('N/A o requiere recargo')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('estudiante_estado')
                                    ->label('Estado Académico')
                                    ->default('N/A o requiere recargo')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                    ])
                    ->columns(2)
                    ->collapsible(),
                Section::make('Información del Curso y Materias')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        KeyValueEntry::make('materias')
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

                        Text::make('grupo_detalle')
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
                            })
                            ->hidden(fn (Get $get) => !filled($get('grupo_id')))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->hidden(fn (Get $get) => !filled($get('grupo_id'))),
                Section::make('Gestión Académica')
                    ->description('Configure la gestión y el grupo para la inscripción')
                    ->icon('heroicon-o-calendar')
                    ->columnSpanFull()
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
                            ->helperText('Seleccione la gestión académica vigente')
                            ->disabled()
                        ,

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
                            ->disabled()
                            ->helperText(fn (Get $get) =>
                            $get('gestion_id')
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
                                    $info .= "📚 Materias asignados: " . $grupo->cursos->count();
                                }

                                return $info;
                            })
                            ->hidden(fn (Get $get) => !filled($get('grupo_id')))
                            ->columnSpanFull(),

                        Repeater::make('condiciones')
                            ->label('Condiciones del Grupo')
                            ->required()
                            ->hidden(fn (Get $get) => !filled($get('grupo_id')))
                            ->schema([
                                Select::make('tipo')
                                    ->label('Tipo de Condición')
                                    ->disabled()
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
                                            ->placeholder('Ej: Mínimo 70%, Mayor a 15 años, etc.')
                                            ->maxLength(255),

                                        Select::make('operador')
                                            ->label('Operador')
                                            ->disabled()
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
                                    ->inline(false),

                                Toggle::make('cumple')
                                    ->label('¿Cumple?')
                                    ->default(false)
                                    ->inline(false)
                                    ->helperText('Marque si el estudiante cumple con esta condición'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->addable(false)
                            ->deletable(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Información de Inscripción')
                    ->description('Datos administrativos de la inscripción')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('codigo_inscripcion')
                            ->label('Código de Inscripción')
                            ->default(fn (string $context) =>
                            $context === 'create'
                                ? 'INS-' . now()->format('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT)
                                : null
                            )
                            ->required()
                            ->unique(Inscripcion::class, 'codigo_inscripcion', ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Código único para identificar esta inscripción')
                            ->readOnly(fn (string $context) => $context === 'edit')
                            ->live()
                            ->suffixIcon('heroicon-o-hashtag'),

                        DatePicker::make('fecha_inscripcion')
                            ->label('Fecha de Inscripción')
                            ->readOnly()
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->suffixIcon('heroicon-o-calendar'),

                        Select::make('estado_id')
                            ->label('Estado')
                            ->searchable()
                            ->options(fn () => Estado::where('tipo', 'inscripcion')->pluck('nombre', 'id'))
                            ->default(fn (string $context) =>
                            $context === 'create'
                                ? Estado::where('nombre', 'LIKE', '%activ%')->first()?->id
                                : null
                            )
                            ->required()
                            ->suffixIcon('heroicon-o-check-badge')
                            ->helperText('Estado de la inscripción'),

                        Placeholder::make('inscripcion_validacion')
                            ->label('Validación de Inscripción')
                            ->content(function (Get $get, string $context) {
                                $estudianteId = $get('estudiante_id');
                                $grupoId = $get('grupo_id');

                                if (!$grupoId || !$estudianteId) {
                                    return 'Seleccione un grupo y estudiante para ver la validación';
                                }

                                $grupo = Grupo::with('cursos')->find($grupoId);
                                if (!$grupo) return 'No se encontró información del grupo';

                                $estudiante = Estudiante::with('persona')->find($estudianteId);
                                if (!$estudiante) return 'No se encontró información del estudiante';

                                // En edición, excluir el registro actual
                                $query = Inscripcion::where('estudiante_id', $estudianteId)
                                    ->where('grupo_id', $grupoId)
                                    ->where('gestion_id', $grupo->gestion_id);

                                if ($context === 'edit') {
                                    $recordId = $get('../../id'); // Ajustar según estructura
                                    if ($recordId) {
                                        $query->where('id', '!=', $recordId);
                                    }
                                }

                                $existente = $query->first();

                                $nombre = $estudiante->persona
                                    ? "{$estudiante->persona->nombre} {$estudiante->persona->apellido_pat}"
                                    : $estudiante->codigo_saga;

                                $info = $existente
                                    ? "⚠️ El estudiante {$nombre} ya está inscrito en este grupo para la gestión seleccionada. Haga las modificaciones correspondientes."
                                    : "✅ El estudiante {$nombre} no está inscrito en este grupo y puede proceder con la inscripción.";

                                return $info;
                            })
                            ->hidden(fn (Get $get) => !filled($get('grupo_id')) || !filled($get('estudiante_id')))
                            ->columnSpanFull(),

                        QrCode::make('codigo_inscripcion')
                            ->data(fn (Get $get) => $get('codigo_inscripcion'))
                            ->size(200)
                            ->alignment('center')
                            ->visible(fn (Get $get) => filled($get('codigo_inscripcion')))
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),



                Section::make('Documentos de Inscripción')
                    ->description('Adjunte todos los documentos necesarios para la inscripción (opcional)')
                    ->icon('heroicon-o-paper-clip')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('documentos')
                            ->label('Documentos')
                            ->relationship('documentos')
                            ->schema([
                                Select::make('tipo_documento_id')
                                    ->label('Tipo de Documento')
                                    ->options(fn () => TipoDocumento::where('tipo', 'inscripcion')->pluck('nombre', 'id'))
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
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->deleteAction(
                                fn (Action $action) => $action
                                    ->requiresConfirmation()
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
