<?php

namespace App\Filament\Pages;

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
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\NoReturn;

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
                            Section::make('Información de Contacto / Secundaria')
                                ->icon(Heroicon::OutlinedPhone)
                                ->columns(2)
                                ->schema([
                                    TextInput::make('telefono_principal')
                                        ->prefixIcon(Heroicon::PhoneArrowDownLeft)
                                        ->label('Teléfono Principal')
                                        ->required()
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
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Ingrese el correo electrónico'),

                                    TextInput::make('direccion')
                                        ->prefixIcon(Heroicon::HomeModern)
                                        ->label('Dirección')
                                        ->maxLength(500)
                                        ->placeholder('Ingrese la dirección completa')
                                ]),

                        ])
                        ->description('Ingrese la información personal del estudiante.'),
                    Wizard\Step::make('Detalles Educativos')
                        ->icon(Heroicon::OutlinedAcademicCap)
                        ->description('Proporcione los detalles de educativos del estudiante.')
                        ->schema([
                            FileUpload::make('foto_url')
                                ->label('Foto del Estudiante')
                                ->image()
                                ->maxSize(2048) // Tamaño máximo en KB
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
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            // Si se está editando y ya tiene valor, no generes nada
                                            if ($record && $record->codigo_saga) {
                                                return;
                                            }

                                            // Genera un nuevo código SAGA
                                            $prefix = 'NO-EST';
                                            $year = now()->year;

                                            // Número correlativo, busca el último registro
                                            $last = \App\Models\Estudiante::orderBy('id', 'desc')->first();
                                            $num = $last ? ($last->id + 1) : 1;

                                            // Formato: EST-2024-001
                                            $generated = sprintf('%s-%s-%03d', $prefix, $year, $num);

                                            $component->state($generated);
                                        })
                                        ->unique(ignoreRecord: true)
                                        ->prefixIcon('heroicon-o-hashtag')
                                        ->readOnly()
                                        ->helperText('Código único del sistema SAGA'),

                                    Select::make('estado_academico')
                                        ->prefixIcon(Heroicon::AcademicCap)
                                        ->label('Estado Académico')
                                        ->searchable()
                                        ->options([
                                            'pendiente_inscripcion'    => 'Pendiente de Inscripción',
                                            'inscrito'                 => 'Inscrito',
                                        ])
                                        ->required()
                                        ->placeholder('Ingrese el estado académico del estudiante'),

                                    Toggle::make('tiene_discapacidad')
                                        ->label('¿Tiene Discapacidad?')
                                        ->live()
                                        ->afterStateUpdated(
                                            function (callable $set, $state) {
                                                if (!$state) {
                                                    $set('discapacidades', []);
                                                }
                                            }
                                        )
                                        ->required(),

                                    TextInput::make('observaciones')
                                        ->prefixIcon(Heroicon::AcademicCap)
                                        ->label('Observaciones')
                                        ->maxLength(1000)
                                        ->placeholder('Ingrese cualquier observación relevante'),
                                ]),
                            Repeater::make('discapacidades')
                                ->label('Discapacidades')
                                ->live()
                                ->hidden( fn (callable $get) => !$get('tiene_discapacidad'))
                                ->schema([
                                    Grid::make(2)->schema([
                                        Select::make('discapacidades')
                                            ->label('Discapacidad')
                                            ->searchable()
                                            ->options(fn () => \App\Models\Discapacidad::pluck('nombre', 'id')->toArray())
                                            ->required()
                                            ->placeholder('Seleccione una discapacidad')
                                            ->createOptionForm([
                                                Grid::make(1)
                                                    ->schema([
                                                        TextInput::make('nombre')
                                                            ->label('Nombre de la Discapacidad')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->placeholder('Ingrese el nombre de la discapacidad'),
                                                    ])
                                            ]),
                                        Textarea::make('observacion')
                                            ->label('Observación')
                                            ->maxLength(500)
                                            ->placeholder('Ingrese una observación sobre la discapacidad')
                                    ])
                                ])
                        ]),
                    Wizard\Step::make('Apoderados')
                        ->icon(Heroicon::OutlinedUserGroup)
                        ->description('Ingrese la información de los apoderados del estudiante.')
                        ->schema([
                            Section::make('Información de Apoderados')
                                ->icon(Heroicon::OutlinedUserGroup)
                                ->schema([
                                    Repeater::make('apoderados')
                                        ->label('Apoderados')
                                        ->deletable(fn (callable $get) => $get('apoderados') && count($get('apoderados')) > 1)
                                        ->minItems(1)
                                        ->required()
                                        ->extraAttributes([
                                            'style' => 'background-color: #00000000;'
                                        ])
                                        ->schema([
                                            Grid::make(2)
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
                                                        ->placeholder('Ingrese el apellido paterno del apoderado'),

                                                    TextInput::make('apod_apellido_mat')
                                                        ->prefixIcon(Heroicon::User)

                                                        ->label('Apellido Materno')
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese el apellido materno del apoderado'),

                                                    TextInput::make('apod_carnet_identidad')
                                                        ->prefixIcon(Heroicon::Identification)
                                                        ->label('Carnet de Identidad')
                                                        ->required()
                                                        ->maxLength(20)
                                                        ->placeholder('Ingrese el número de carnet de identidad del apoderado'),

                                                    TextInput::make('apod_telefono_principal')
                                                        ->prefixIcon(Heroicon::Phone)
                                                        ->label('Teléfono')
                                                        ->required()
                                                        ->maxLength(15)
                                                        ->placeholder('Ingrese el teléfono del apoderado'),

                                                    TextInput::make('apod_telefono_secundario')
                                                        ->label('Teléfono')
                                                        ->prefixIcon(Heroicon::Phone)
                                                        ->required()
                                                        ->maxLength(15)
                                                        ->placeholder('Ingrese el teléfono del apoderado'),

                                                    TextInput::make('apod_email_personal')
                                                        ->label('Correo Electrónico')
                                                        ->prefixIcon(Heroicon::Envelope)
                                                        ->email()
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese el correo electrónico del apoderado'),
                                                    TextInput::make('apod_direccion')
                                                        ->label('Dirección')
                                                        ->prefixIcon(Heroicon::HomeModern)
                                                        ->maxLength(500)
                                                        ->email()
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese del apoderado del apoderado'),
                                                ]),
                                            Section::make('Información Laboral del Apoderado')
                                                ->icon(Heroicon::Briefcase)
                                                ->columns(2)
                                                ->schema([
                                                    TextInput::make('apod_ocupacion')
                                                        ->label('Ocupación')
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese la ocupación del apoderado'),

                                                    TextInput::make('apod_empresa')
                                                        ->label('Lugar de Trabajo')
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese el lugar de trabajo del apoderado'),

                                                    TextInput::make('apod_cargo_empresa')
                                                        ->label('Cargo')
                                                        ->maxLength(255)
                                                        ->placeholder('Ingrese el cargo del apoderado en su trabajo'),

                                                    Select::make('apod_nivel_educacion')
                                                        ->label('Nivel de Educación')
                                                        ->searchable()
                                                        ->options([
                                                            'ninguno'               => 'Ninguno',
                                                            'primaria_incompleta'   => 'Primaria Incompleta',
                                                            'primaria_completa'     => 'Primaria Completa',
                                                            'secundaria_incompleta' => 'Secundaria Incompleta',
                                                            'secundaria_completa'   => 'Secundaria Completa',
                                                            'bachillerato_incompleto'=> 'Bachillerato Incompleto',
                                                            'bachillerato_completo' => 'Bachillerato Completo',
                                                            'educacion_superior'    => 'Educación Superior',
                                                            'postgrado'             => 'Postgrado',
                                                        ])
                                                        ->placeholder('Ingrese el nivel de educación del apoderado'),

                                                    Select::make('apod_estado_civil')
                                                        ->label('Estado Civil')
                                                        ->searchable()
                                                        ->options([
                                                            'soltero'       => 'Soltero(a)',
                                                            'casado'        => 'Casado(a)',
                                                            'divorciado'    => 'Divorciado(a)',
                                                            'viudo'         => 'Viudo(a)',
                                                            'union_libre'   => 'Unión Libre',
                                                        ])
                                                        ->placeholder('Ingrese el estado civil del apoderado'),
                                                ]),
                                            Section::make('Reñacion con el Estudiante')
                                                ->icon(Heroicon::OutlinedUserGroup)
                                                ->columns(3)
                                                ->schema([
                                                    Select::make('apod_parentesco')
                                                        ->label('Parentesco')
                                                        ->searchable()
                                                        ->options([
                                                            'padre'         => 'Padre',
                                                            'madre'         => 'Madre',
                                                            'tutor'         => 'Tutor',
                                                            'hermano'       => 'Hermano(a)',
                                                            'abuelo'        => 'Abuelo(a)',
                                                            'otro'          => 'Otro',
                                                        ])
                                                        ->required()
                                                        ->placeholder('Seleccione el parentesco'),

                                                    Toggle::make('apod_vive_con_el')
                                                        ->label('¿Vive con el Estudiante?')
                                                        ->onIcon(Heroicon::OutlinedHome)
                                                        ->offIcon(Heroicon::Home)
                                                        ->required(),

                                                    Toggle::make('apod_es_principal')
                                                        ->label('¿Es Apoderado Principal?')
                                                        ->onIcon(Heroicon::OutlinedStar)
                                                        ->offIcon(Heroicon::Star)
                                                        ->required(),
                                                ])
                                        ])
                                ])
                        ])
                ])
                    ->columnSpanFull()
                    ->submitAction(view('filament.pages.components.submit-button-estudiante'))
                    ->persistStepInQueryString()
                    ->skippable(true)
            ])->statePath('data');

    }

    public function create(): void
    {
        try {
            $data = $this->form->getState();

        } catch (Halt $exception) {
            return;
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error al agregar al estudiante')
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
            Action::make('view_persons')
                ->label('Ver estudiantes')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(route('filament.informatica.resources.personas.index')),
        ];
    }

    public function getFooter(): ?View
    {
        return view('filament.pages.footer-inscripcion-avanzada');
    }
}
