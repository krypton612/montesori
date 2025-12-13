<?php

namespace App\Filament\Pages;

use App\Models\Apoderado;
use App\Models\Estudiante;
use App\Models\Persona;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class CrearEstudianteAvanzada extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.crear-estudiante-avanzada';

    protected static string|null|\UnitEnum $navigationGroup = 'Inscripcion Estudiantil';

    protected static ?string $navigationLabel = 'Registro Estudiantil';

    protected static ?string $title = 'Formulario de Registro Avanzado';

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
                    Wizard\Step::make('Datos Personales')
                        ->description('Ingrese la información personal del estudiante.')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Section::make('Información Personal')
                                ->icon(Heroicon::OutlinedUser)
                                ->columns(2)
                                ->schema([
                                    TextInput::make('nombre')
                                        ->prefixIcon(Heroicon::AcademicCap)
                                        ->label('Nombre')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Ingrese el nombre completo'),

                                    TextInput::make('apellido_pat')
                                        ->prefixIcon(Heroicon::AcademicCap)
                                        ->label('Apellido Paterno')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Ingrese el apellido paterno'),

                                    TextInput::make('apellido_mat')
                                        ->prefixIcon(Heroicon::AcademicCap)
                                        ->label('Apellido Materno')
                                        ->maxLength(255)
                                        ->placeholder('Ingrese el apellido materno'),

                                    TextInput::make('carnet_identidad')
                                        ->prefixIcon(Heroicon::Identification)
                                        ->label('Carnet de Identidad')
                                        ->required()
                                        ->maxLength(20)
                                        ->unique(Persona::class, 'carnet_identidad')
                                        ->placeholder('Ingrese el número de carnet de identidad'),

                                    TextInput::make('fecha_nacimiento')
                                        ->prefixIcon(Heroicon::Calendar)
                                        ->label('Fecha de Nacimiento')
                                        ->required()
                                        ->type('date')
                                        ->placeholder('YYYY-MM-DD'),

                                    Toggle::make('habilitado')
                                        ->label('¿Habilitado?')
                                        ->onIcon(Heroicon::OutlinedCheck)
                                        ->offIcon(Heroicon::CheckBadge)
                                        ->default(true)
                                        ->required(),
                                ]),

                            Section::make('Información de Contacto')
                                ->icon(Heroicon::OutlinedPhone)
                                ->columns(2)
                                ->schema([
                                    TextInput::make('telefono_principal')
                                        ->prefixIcon(Heroicon::PhoneArrowDownLeft)
                                        ->label('Teléfono Principal')
                                        ->maxLength(15)
                                        ->placeholder('Ingrese el teléfono principal'),

                                    TextInput::make('telefono_secundario')
                                        ->prefixIcon(Heroicon::PhoneArrowDownLeft)
                                        ->label('Teléfono Secundario')
                                        ->maxLength(15)
                                        ->placeholder('Ingrese el teléfono secundario'),

                                    TextInput::make('email_personal')
                                        ->prefixIcon(Heroicon::Envelope)
                                        ->label('Correo Electrónico')
                                        ->email()
                                        ->maxLength(255)
                                        ->placeholder('Ingrese el correo electrónico'),

                                    TextInput::make('direccion')
                                        ->prefixIcon(Heroicon::HomeModern)
                                        ->label('Dirección')
                                        ->maxLength(500)
                                        ->placeholder('Ingrese la dirección completa')
                                ]),
                        ]),

                    Wizard\Step::make('Detalles Educativos')
                        ->icon(Heroicon::OutlinedAcademicCap)
                        ->description('Proporcione los detalles educativos del estudiante.')
                        ->schema([
                            FileUpload::make('foto_url')
                                ->label('Foto del Estudiante')
                                ->image()
                                ->disk('public')
                                ->directory('estudiantes/fotos')
                                ->maxSize(2048)
                                ->imageEditor()
                                ->placeholder('Suba una foto del estudiante (opcional)')
                                ->columnSpanFull(),

                            Section::make('Información Académica')
                                ->icon(Heroicon::OutlinedAcademicCap)
                                ->columns(2)
                                ->schema([
                                    TextInput::make('codigo_saga')
                                        ->label('Código SAGA')
                                        ->maxLength(50)
                                        ->placeholder('Ej: EST-2024-001')
                                        ->default(function () {
                                            $prefix = 'NO-EST';
                                            $year = now()->year;
                                            $last = Estudiante::orderBy('id', 'desc')->first();
                                            $num = $last ? ($last->id + 1) : 1;
                                            return sprintf('%s-%s-%03d', $prefix, $year, $num);
                                        })
                                        ->unique(Estudiante::class, 'codigo_saga', ignoreRecord: true)
                                        ->prefixIcon('heroicon-o-hashtag')
                                        ->readOnly()
                                        ->helperText('Código único del sistema SAGA'),

                                    Select::make('estado_academico')
                                        ->prefixIcon(Heroicon::AcademicCap)
                                        ->label('Estado Académico')
                                        ->searchable()
                                        ->options([
                                            'pendiente_inscripcion' => 'Pendiente de Inscripción',
                                            'inscrito' => 'Inscrito',
                                        ])
                                        ->default('pendiente_inscripcion')
                                        ->required()
                                        ->placeholder('Seleccione el estado académico'),

                                    Toggle::make('tiene_discapacidad')
                                        ->label('¿Tiene Discapacidad?')
                                        ->live()
                                        ->default(false)
                                        ->afterStateUpdated(function (callable $set, $state) {
                                            if (!$state) {
                                                $set('discapacidades', []);
                                            }
                                        })
                                        ->columnSpanFull(),

                                    Textarea::make('observaciones')
                                        ->label('Observaciones')
                                        ->rows(3)
                                        ->maxLength(1000)
                                        ->placeholder('Ingrese cualquier observación relevante')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Discapacidades del Estudiante')
                                ->icon(Heroicon::OutlinedExclamationTriangle)
                                ->description('Agregue las discapacidades que presenta el estudiante.')
                                ->hidden(fn(callable $get) => !$get('tiene_discapacidad'))
                                ->schema([
                                    Repeater::make('discapacidades')
                                        ->label('Lista de Discapacidades')
                                        ->schema([
                                            Select::make('discapacidad_id')
                                                ->label('Discapacidad')
                                                ->searchable()
                                                ->preload()
                                                ->options(fn() => \App\Models\Discapacidad::where('visible', true)
                                                    ->pluck('nombre', 'id'))
                                                ->required()
                                                ->placeholder('Seleccione una discapacidad')
                                                ->columnSpan(1),

                                            Textarea::make('observacion')
                                                ->label('Observación')
                                                ->rows(2)
                                                ->maxLength(500)
                                                ->placeholder('Detalles adicionales sobre esta discapacidad')
                                                ->columnSpan(1),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel('Agregar Discapacidad')
                                        ->collapsible()
                                        ->itemLabel(fn(array $state): ?string =>
                                            \App\Models\Discapacidad::find($state['discapacidad_id'] ?? null)?->nombre ?? 'Nueva discapacidad'
                                        )
                                        ->columnSpanFull()
                                        ->reorderable(false)
                                        ->deleteAction(
                                            fn(Action $action) => $action
                                                ->requiresConfirmation()
                                                ->modalHeading('Eliminar Discapacidad')
                                                ->modalDescription('¿Está seguro de que desea eliminar esta discapacidad?')
                                        ),
                                ]),
                        ]),

                    Wizard\Step::make('Apoderados')
                        ->icon(Heroicon::OutlinedUserGroup)
                        ->description('Ingrese la información de los apoderados del estudiante.')
                        ->schema([
                            Section::make('Información de Apoderados')
                                ->icon(Heroicon::OutlinedUserGroup)
                                ->description('El estudiante debe tener al menos un apoderado registrado.')
                                ->schema([
                                    Repeater::make('apoderados')
                                        ->label('Apoderados del Estudiante')
                                        ->minItems(1)
                                        ->defaultItems(1)
                                        ->required()
                                        ->collapsible()
                                        ->itemLabel(fn(array $state): ?string =>
                                        isset($state['apod_nombre'], $state['apod_apellido_pat'])
                                            ? "{$state['apod_nombre']} {$state['apod_apellido_pat']}"
                                            : 'Nuevo Apoderado'
                                        )
                                        ->schema([
                                            Section::make('Datos Personales del Apoderado')
                                                ->icon(Heroicon::OutlinedUser)
                                                ->columns(2)
                                                ->schema([
                                                    TextInput::make('apod_nombre')
                                                        ->prefixIcon(Heroicon::User)
                                                        ->label('Nombre')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese el nombre del apoderado'),

                                                    TextInput::make('apod_apellido_pat')
                                                        ->prefixIcon(Heroicon::User)
                                                        ->label('Apellido Paterno')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese el apellido paterno'),

                                                    TextInput::make('apod_apellido_mat')
                                                        ->prefixIcon(Heroicon::User)
                                                        ->label('Apellido Materno')
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese el apellido materno'),

                                                    TextInput::make('apod_carnet_identidad')
                                                        ->prefixIcon(Heroicon::Identification)
                                                        ->label('Carnet de Identidad')
                                                        ->required()
                                                        ->maxLength(20)
                                                        ->placeholder('Ingrese el CI del apoderado'),

                                                    TextInput::make('apod_fecha_nacimiento')
                                                        ->prefixIcon(Heroicon::Calendar)
                                                        ->label('Fecha de Nacimiento')
                                                        ->type('date')
                                                        ->placeholder('YYYY-MM-DD'),

                                                    Toggle::make('apod_habilitado')
                                                        ->label('¿Habilitado?')
                                                        ->default(true)
                                                        ->inline(false),
                                                ]),

                                            Section::make('Información de Contacto del Apoderado')
                                                ->icon(Heroicon::OutlinedPhone)
                                                ->columns(2)
                                                ->schema([
                                                    TextInput::make('apod_telefono_principal')
                                                        ->prefixIcon(Heroicon::Phone)
                                                        ->label('Teléfono Principal')
                                                        ->required()
                                                        ->maxLength(15)
                                                        ->placeholder('Teléfono principal'),

                                                    TextInput::make('apod_telefono_secundario')
                                                        ->prefixIcon(Heroicon::Phone)
                                                        ->label('Teléfono Secundario')
                                                        ->maxLength(15)
                                                        ->placeholder('Teléfono secundario'),

                                                    TextInput::make('apod_email_personal')
                                                        ->prefixIcon(Heroicon::Envelope)
                                                        ->label('Correo Electrónico')
                                                        ->email()
                                                        ->maxLength(255)
                                                        ->placeholder('correo@ejemplo.com'),

                                                    TextInput::make('apod_direccion')
                                                        ->prefixIcon(Heroicon::HomeModern)
                                                        ->label('Dirección')
                                                        ->maxLength(500)
                                                        ->placeholder('Dirección completa')
                                                        ->columnSpanFull(),
                                                ]),

                                            Section::make('Información Laboral del Apoderado')
                                                ->icon(Heroicon::Briefcase)
                                                ->columns(2)
                                                ->collapsible()
                                                ->schema([
                                                    TextInput::make('apod_ocupacion')
                                                        ->label('Ocupación')
                                                        ->maxLength(255)
                                                        ->placeholder('Ocupación del apoderado'),

                                                    TextInput::make('apod_empresa')
                                                        ->label('Lugar de Trabajo')
                                                        ->maxLength(255)
                                                        ->placeholder('Empresa o institución'),

                                                    TextInput::make('apod_cargo_empresa')
                                                        ->label('Cargo')
                                                        ->maxLength(255)
                                                        ->placeholder('Cargo que desempeña'),

                                                    Select::make('apod_nivel_educacion')
                                                        ->label('Nivel de Educación')
                                                        ->searchable()
                                                        ->options([
                                                            'ninguno' => 'Ninguno',
                                                            'primaria_incompleta' => 'Primaria Incompleta',
                                                            'primaria_completa' => 'Primaria Completa',
                                                            'secundaria_incompleta' => 'Secundaria Incompleta',
                                                            'secundaria_completa' => 'Secundaria Completa',
                                                            'bachillerato_incompleto' => 'Bachillerato Incompleto',
                                                            'bachillerato_completo' => 'Bachillerato Completo',
                                                            'educacion_superior' => 'Educación Superior',
                                                            'postgrado' => 'Postgrado',
                                                        ])
                                                        ->placeholder('Nivel educativo'),

                                                    Select::make('apod_estado_civil')
                                                        ->label('Estado Civil')
                                                        ->searchable()
                                                        ->options([
                                                            'soltero' => 'Soltero(a)',
                                                            'casado' => 'Casado(a)',
                                                            'divorciado' => 'Divorciado(a)',
                                                            'viudo' => 'Viudo(a)',
                                                            'union_libre' => 'Unión Libre',
                                                        ])
                                                        ->placeholder('Estado civil'),
                                                ]),

                                            Section::make('Relación con el Estudiante')
                                                ->icon(Heroicon::OutlinedUserGroup)
                                                ->columns(3)
                                                ->schema([
                                                    Select::make('apod_parentesco')
                                                        ->label('Parentesco')
                                                        ->searchable()
                                                        ->options([
                                                            'padre' => 'Padre',
                                                            'madre' => 'Madre',
                                                            'tutor' => 'Tutor',
                                                            'hermano' => 'Hermano(a)',
                                                            'abuelo' => 'Abuelo(a)',
                                                            'tio' => 'Tío(a)',
                                                            'otro' => 'Otro',
                                                        ])
                                                        ->required()
                                                        ->placeholder('Seleccione el parentesco'),

                                                    Toggle::make('apod_vive_con_el')
                                                        ->label('¿Vive con el Estudiante?')
                                                        ->onIcon(Heroicon::OutlinedHome)
                                                        ->offIcon(Heroicon::Home)
                                                        ->default(false)
                                                        ->inline(false),

                                                    Toggle::make('apod_es_principal')
                                                        ->label('¿Es Apoderado Principal?')
                                                        ->onIcon(Heroicon::OutlinedStar)
                                                        ->offIcon(Heroicon::Star)
                                                        ->default(false)
                                                        ->helperText('Solo puede haber un apoderado principal')
                                                        ->inline(false),
                                                ]),
                                        ])
                                        ->deleteAction(
                                            fn(Action $action) => $action
                                                ->requiresConfirmation()
                                                ->modalHeading('Eliminar Apoderado')
                                                ->modalDescription('¿Está seguro de eliminar este apoderado?')
                                        )
                                        ->addActionLabel('Agregar Otro Apoderado')
                                        ->reorderableWithButtons()
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                    ->columnSpanFull()
                    ->submitAction(view('filament.pages.components.submit-button-estudiante'))
                    ->persistStepInQueryString()
                    ->skippable(false)
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        try {
            DB::beginTransaction();

            $data = $this->form->getState();

            // 1. Crear la Persona del Estudiante
            $personaEstudiante = Persona::create([
                'nombre' => $data['nombre'],
                'apellido_pat' => $data['apellido_pat'],
                'apellido_mat' => $data['apellido_mat'] ?? null,
                'carnet_identidad' => $data['carnet_identidad'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'telefono_principal' => $data['telefono_principal'],
                'telefono_secundario' => $data['telefono_secundario'] ?? null,
                'email_personal' => $data['email_personal'],
                'direccion' => $data['direccion'] ?? null,
                'habilitado' => $data['habilitado'] ?? true,
            ]);

            // 2. Crear el Estudiante
            $estudiante = Estudiante::create([
                'persona_id' => $personaEstudiante->id,
                'codigo_saga' => $data['codigo_saga'],
                'estado_academico' => $data['estado_academico'],
                'tiene_discapacidad' => $data['tiene_discapacidad'] ?? false,
                'observaciones' => $data['observaciones'] ?? null,
                'foto_url' => $data['foto_url'] ?? null,
            ]);

            // 3. Asociar Discapacidades si existen
            if (!empty($data['discapacidades']) && $data['tiene_discapacidad']) {
                foreach ($data['discapacidades'] as $discapacidad) {
                    if (isset($discapacidad['discapacidad_id'])) {
                        $estudiante->discapacidades()->attach(
                            $discapacidad['discapacidad_id'],
                            ['observacion' => $discapacidad['observacion'] ?? null]
                        );
                    }
                }
            }

            // 4. Crear Apoderados y Relaciones
            if (!empty($data['apoderados'])) {
                $hasPrincipal = false;

                foreach ($data['apoderados'] as $index => $apoderadoData) {
                    // Verificar si ya existe una persona con este CI
                    $personaApoderado = Persona::where('carnet_identidad', $apoderadoData['apod_carnet_identidad'])->first();

                    // Si no existe, crear nueva persona
                    if (!$personaApoderado) {
                        $personaApoderado = Persona::create([
                            'nombre' => $apoderadoData['apod_nombre'],
                            'apellido_pat' => $apoderadoData['apod_apellido_pat'],
                            'apellido_mat' => $apoderadoData['apod_apellido_mat'] ?? null,
                            'carnet_identidad' => $apoderadoData['apod_carnet_identidad'],
                            'fecha_nacimiento' => $apoderadoData['apod_fecha_nacimiento'] ?? null,
                            'telefono_principal' => $apoderadoData['apod_telefono_principal'],
                            'telefono_secundario' => $apoderadoData['apod_telefono_secundario'] ?? null,
                            'email_personal' => $apoderadoData['apod_email_personal'] ?? null,
                            'direccion' => $apoderadoData['apod_direccion'] ?? null,
                            'habilitado' => $apoderadoData['apod_habilitado'] ?? true,
                        ]);
                    }

                    // Buscar o crear el Apoderado
                    $apoderado = Apoderado::where('persona_id', $personaApoderado->id)->first();

                    if (!$apoderado) {
                        $apoderado = Apoderado::create([
                            'persona_id' => $personaApoderado->id,
                            'ocupacion' => $apoderadoData['apod_ocupacion'] ?? null,
                            'empresa' => $apoderadoData['apod_empresa'] ?? null,
                            'cargo_empresa' => $apoderadoData['apod_cargo_empresa'] ?? null,
                            'nivel_educacion' => $apoderadoData['apod_nivel_educacion'] ?? null,
                            'estado_civil' => $apoderadoData['apod_estado_civil'] ?? null,
                        ]);
                    }

                    // Validar que solo haya un apoderado principal
                    $esPrincipal = $apoderadoData['apod_es_principal'] ?? false;
                    if ($esPrincipal && $hasPrincipal) {
                        $esPrincipal = false; // Si ya hay uno principal, este no lo será
                    }
                    if ($esPrincipal) {
                        $hasPrincipal = true;
                    }

                    // Si es el primer apoderado y no hay ninguno marcado como principal, hacerlo principal
                    if ($index === 0 && !$hasPrincipal) {
                        $esPrincipal = true;
                        $hasPrincipal = true;
                    }

                    // Relacionar Apoderado con Estudiante en la tabla pivote
                    $estudiante->apoderados()->attach($apoderado->id, [
                        'parentestco' => $apoderadoData['apod_parentesco'],
                        'vive_con_el' => $apoderadoData['apod_vive_con_el'] ?? false,
                        'es_principal' => $esPrincipal,
                    ]);
                }
            }

            DB::commit();

            Notification::make()
                ->title('¡Estudiante registrado exitosamente!')
                ->success()
                ->body("El estudiante {$personaEstudiante->nombre_completo} ha sido registrado con el código {$estudiante->codigo_saga}")
                ->duration(5000)
                ->send();

            // Resetear el formulario
            $this->form->fill();

            // Opcional: Redirigir a la vista de estudiantes
            $this->redirect(route('filament.informatica.resources.estudiantes.index'));

        } catch (Halt $exception) {
            // Filament detuvo el proceso, no hacer nada
            return;
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error al registrar estudiante')
                ->danger()
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->persistent()
                ->send();

            // Log del error para debugging
            \Log::error('Error al crear estudiante:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function getSubheading(): ?string
    {
        return 'Complete el formulario para inscribir a un estudiante en un grupo específico para la gestión académica seleccionada.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_persons')
                ->label('Ver estudiantes')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(route('filament.informatica.resources.estudiantes.index')),
        ];
    }

    public function getFooter(): ?View
    {
        return view('filament.pages.footer-inscripcion-avanzada');
    }
}
