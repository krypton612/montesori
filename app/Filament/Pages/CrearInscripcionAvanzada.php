<?php

namespace App\Filament\Pages;

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
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class CrearInscripcionAvanzada extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.crear-inscripcion-avanzada';

    protected static string|null|\UnitEnum $navigationGroup = 'Inscripcion Estudiantil';

    protected static ?string $navigationLabel = 'Inscripción Avanzada';

    protected static ?string $title = 'Formulario de Inscripción Avanzada';


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
                                                    $set('estudiante_ci', $estudiante->persona->ci ?? 'N/A');
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
                                                ->disabled()
                                                ,

                                            TextInput::make('estudiante_ci')
                                                ->label('Cédula de Identidad')
                                                ->default('N/A o requiere recargo')
                                                ->disabled()
                                                ,

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
                                                $grupo = \App\Models\Grupo::find($state);
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
                                                $info .= "📚 Cursos asignados: " . $grupo->cursos->count();
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
                                    Section::make('Estudiante y grupo')
                                        ->columnSpan(2)
                                        ->schema([
                                            Textarea::make('perros')
                                                ->label('Estudiante')
                                        ])

                                ])
                                ->columns(3),
                        ]),

                    Wizard\Step::make('Documentos')
                        ->description('Adjunte los documentos requeridos')
                        ->icon('heroicon-o-paper-clip')
                        ->schema([
                            Section::make('Documentos de Inscripción')
                                ->description('Adjunte todos los documentos necesarios para la inscripción (opcional)')
                                ->schema([
                                    Repeater::make('documentos')
                                        ->label('Documentos')
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
                                ->icon('heroicon-o-document-duplicate'),
                        ]),
                ])
                    ->columnSpanFull()
                    ->submitAction(view('filament.pages.components.submit-button'))
                    ->persistStepInQueryString()
                    ->skippable(true)
                    ,

            ])
            ->statePath('data');
    }

    public function create(): void
    {
        try {
            $data = $this->form->getState();

            // Validar que no exista una inscripción duplicada
            $existente = Inscripcion::where('estudiante_id', $data['estudiante_id'])
                ->where('grupo_id', $data['grupo_id'])
                ->where('gestion_id', $data['gestion_id'])
                ->first();

            if ($existente) {
                Notification::make()
                    ->title('Inscripción duplicada')
                    ->body('El estudiante ya está inscrito en este grupo para la gestión seleccionada.')
                    ->danger()
                    ->send();

                throw new Halt();
            }

            DB::beginTransaction();

            // Crear la inscripción
            $inscripcion = Inscripcion::create([
                'codigo_inscripcion' => $data['codigo_inscripcion'],
                'estudiante_id' => $data['estudiante_id'],
                'grupo_id' => $data['grupo_id'],
                'gestion_id' => $data['gestion_id'],
                'fecha_inscripcion' => $data['fecha_inscripcion'],
                'estado_id' => $data['estado_id'],
            ]);

            // Procesar documentos si existen
            if (!empty($data['documentos'])) {
                foreach ($data['documentos'] as $documento) {
                    $inscripcion->documentos()->create([
                        'tipo_documento_id' => $documento['tipo_documento_id'],
                        'nombre_archivo' => $documento['nombre_archivo'],
                    ]);
                }
            }

            DB::commit();

            Notification::make()
                ->title('¡Inscripción creada exitosamente!')
                ->body("La inscripción {$data['codigo_inscripcion']} ha sido registrada correctamente.")
                ->success()
                ->seconds(5)
                ->send();

            // Redirigir a la lista de inscripciones
            $this->redirect(route('filament.informatica.resources.inscripcions.index'));

        } catch (Halt $exception) {
            return;
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
                ->url(route('filament.informatica.resources.inscripcions.index')),
        ];
    }

    public function getFooter(): ?View
    {
        return view('filament.pages.footer-inscripcion-avanzada');
    }
}
