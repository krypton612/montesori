<?php

namespace App\Filament\Inscripcion\Pages;

use App\Models\Apoderado;
use App\Models\Estudiante;
use App\Models\Persona;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class CrearEstudianteAvanzada extends Page implements HasForms
{
    use InteractsWithForms, HasPageShield;

    protected string $view = 'filament.pages.crear-estudiante-avanzada';

    protected static string|null|\UnitEnum $navigationGroup = 'Inscripcion Estudiantil';
    protected static ?string $navigationLabel = 'Registro Estudiantil';
    protected static ?string $title = 'Formulario de Registro Avanzado';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    // =========================================================
    //  Helpers para que los errores se vean en inputs (statePath)
    // =========================================================

    protected function prefixStatePathErrors(array $errors): array
    {
        $prefixed = [];
        foreach ($errors as $key => $message) {
            $k = str_starts_with($key, 'data.') ? $key : "data.$key";
            $prefixed[$k] = $message;
        }
        return $prefixed;
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
            throw ValidationException::withMessages($this->prefixStatePathErrors($fieldErrors));
        }

        throw new Halt();
    }

    /**
     * Para Wizard Next: NUNCA uses addError() + Halt,
     * porque puede frenar el paso sin pintar errores.
     * Esto garantiza errores visibles en los campos.
     */
    protected function failStep(string $title, string $body, array $fieldErrors = []): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->danger()
            ->persistent()
            ->send();

        throw ValidationException::withMessages(
            $this->prefixStatePathErrors($fieldErrors ?: ['_step' => 'No se puede continuar. Revise los campos.'])
        );
    }

    // =========================
    // Normalizadores / validadores
    // =========================

    protected function normalizeSpaces(?string $value): string
    {
        $value = trim((string) $value);
        return preg_replace('/\s+/u', ' ', $value) ?: '';
    }

    protected function containsAccents(string $value): bool
    {
        return (bool) preg_match('/[ÁÉÍÓÚáéíóúÜü]/u', $value);
    }

    protected function isValidBoliviaName(string $value): bool
    {
        // Solo letras sin tildes, espacios, guion y apóstrofe. Permite Ñ/ñ.
        return (bool) preg_match("/^[A-Za-zÑñ\s'\-]+$/u", $value);
    }

    /**
     * Normaliza teléfonos Bolivia:
     * - quita espacios/guiones/paréntesis
     * - quita prefijo +591 o 591 si viene
     * - devuelve solo dígitos o null
     */
    protected function normalizeBoliviaMobile($value): ?string
    {
        $v = trim((string) ($value ?? ''));
        if ($v === '') return null;

        $digits = preg_replace('/\D+/', '', $v);

        if (str_starts_with($digits, '591')) {
            $digits = substr($digits, 3);
        }

        return $digits === '' ? null : $digits;
    }

    protected function isValidBoliviaMobile(?string $digits): bool
    {
        if (empty($digits)) return false;
        // Bolivia móvil: 8 dígitos y comienza con 6 o 7
        return (bool) preg_match('/^[67]\d{7}$/', $digits);
    }

    protected function normalizeCiDigits($value): string
    {
        return preg_replace('/\D+/', '', (string) ($value ?? ''));
    }

    protected function isValidCi(string $ci): bool
    {
        return (bool) preg_match('/^\d{7,12}$/', $ci);
    }

    // =========================================================
    //  VALIDACIONES EN CADA NEXT (Wizard)
    // =========================================================

    protected function validateStepDatosPersonales(): void
    {
        $data = (array) ($this->data ?? []);
        $errors = [];

        // normalizaciones suaves
        foreach (['nombre', 'apellido_pat', 'apellido_mat', 'direccion'] as $f) {
            if (isset($data[$f])) {
                $data[$f] = $this->normalizeSpaces($data[$f]);
            }
        }

        // nombres sin tildes + formato
        foreach (['nombre' => 'Nombre', 'apellido_pat' => 'Apellido Paterno', 'apellido_mat' => 'Apellido Materno'] as $field => $label) {
            $val = trim((string) ($data[$field] ?? ''));

            if ($field !== 'apellido_mat' && $val === '') {
                $errors[$field] = "Debe ingresar el {$label}.";
                continue;
            }

            if ($val !== '') {
                if ($this->containsAccents($val)) {
                    $errors[$field] = "{$label}: no use tildes/acentos (ej: José → Jose).";
                } elseif (!$this->isValidBoliviaName($val)) {
                    $errors[$field] = "{$label}: solo letras, espacios, guion o apóstrofe (sin números).";
                } elseif (mb_strlen($val) < 2) {
                    $errors[$field] = "{$label}: es demasiado corto.";
                }
            }
        }

        // CI estudiante: numérico >= 7
        $ci = $this->normalizeCiDigits($data['carnet_identidad'] ?? null);
        $data['carnet_identidad'] = $ci;

        if ($ci === '' || !$this->isValidCi($ci)) {
            $errors['carnet_identidad'] = 'El CI debe contener solo números y tener al menos 7 dígitos (máx. 12).';
        } else {
            if (Persona::where('carnet_identidad', $ci)->exists()) {
                $errors['carnet_identidad'] = 'Ya existe una persona registrada con este CI.';
            }
        }

        $fechaNac = $data['fecha_nacimiento'] ?? null;
        if (empty($fechaNac)) {
            $errors['fecha_nacimiento'] = 'Debe ingresar la fecha de nacimiento.';
        } else {
            try {
                $fn = Carbon::parse($fechaNac)->startOfDay();
                if ($fn->greaterThan(Carbon::today())) {
                    $errors['fecha_nacimiento'] = 'La fecha de nacimiento no puede ser futura.';
                }elseif ($fn->lessThan(Carbon::today()->subYears(120))) {
                    $errors['fecha_nacimiento'] = 'La fecha de nacimiento parece inválida (muy antigua).';
                }
            } catch (\Throwable $e) {
                $errors['fecha_nacimiento'] = 'La fecha de nacimiento tiene un formato inválido.';
            }
        }

        // teléfonos estudiante: Bolivia móvil normalizado (OPCIONAL)
        $tel1 = $this->normalizeBoliviaMobile($data['telefono_principal'] ?? null);
        $data['telefono_principal'] = $tel1;

        // si viene, valida; si no viene, NO error
        if (!empty($tel1) && !$this->isValidBoliviaMobile($tel1)) {
            $errors['telefono_principal'] = 'Teléfono principal inválido: 8 dígitos y debe empezar con 6 o 7 (Bolivia).';
        }

        $tel2 = $this->normalizeBoliviaMobile($data['telefono_secundario'] ?? null);
        $data['telefono_secundario'] = $tel2;

        if (!empty($tel2) && !$this->isValidBoliviaMobile($tel2)) {
            $errors['telefono_secundario'] = 'Teléfono secundario inválido: 8 dígitos y debe empezar con 6 o 7 (Bolivia).';
        }

        // email
        $email = trim((string) ($data['email_personal'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_personal'] = 'El correo electrónico no es válido.';
        }

        // Email único en persona (si viene)
        if ($email !== '' && Persona::where('email_personal', $email)->exists()) {
            $errors['email_personal'] = 'Este correo ya está registrado en otra persona.';
        }

        // guarda normalizaciones en el estado livewire
        $this->data = array_merge($this->data ?? [], $data);

        if (!empty($errors)) {
            $this->failStep('No se puede continuar', 'Corrija los campos marcados para avanzar.', $errors);
        }
    }

    protected function validateStepDetallesEducativos(): void
    {
        $data = (array) ($this->data ?? []);
        $errors = [];

        // Estado académico: aquí NO inscrito
        if (($data['estado_academico'] ?? null) === 'inscrito') {
            $errors['estado_academico'] = 'No puede registrar como "Inscrito" aquí. Use la página de Inscripción.';
        }

        // Discapacidad: si marca, exige lista + sin duplicados
        $tieneDis = (bool) ($data['tiene_discapacidad'] ?? false);
        $discs = collect($data['discapacidades'] ?? []);

        if ($tieneDis) {
            if ($discs->isEmpty()) {
                $errors['discapacidades'] = 'Marcó "Tiene Discapacidad", pero no agregó ninguna.';
            } else {
                $ids = $discs->pluck('discapacidad_id')->filter()->values();
                if ($ids->count() !== $ids->unique()->count()) {
                    $errors['discapacidades'] = 'No puede repetir la misma discapacidad más de una vez.';
                }
            }
        }

        if (!empty($errors)) {
            $this->failStep('No se puede continuar', 'Revise los campos marcados para avanzar.', $errors);
        }
    }

    protected function validateStepApoderados(): void
    {
        $data = (array) ($this->data ?? []);
        $errors = [];

        $ciEst = $this->normalizeCiDigits($data['carnet_identidad'] ?? null);

        $apods = collect($data['apoderados'] ?? []);
        if ($apods->isEmpty()) {
            $errors['apoderados'] = 'Debe registrar al menos un apoderado.';
        } else {
            // EXACTAMENTE 1 principal
            $principalCount = $apods->filter(fn ($a) => (bool) ($a['apod_es_principal'] ?? false))->count();
            if ($principalCount === 0) {
                $errors['apoderados'] = 'Debe marcar exactamente 1 apoderado como principal.';
            } elseif ($principalCount > 1) {
                $errors['apoderados'] = 'Solo puede existir 1 apoderado principal. Desmarque los demás.';
            }

            // Duplicados por CI
            $cisApods = $apods->map(fn ($a) => $this->normalizeCiDigits($a['apod_carnet_identidad'] ?? null))
                ->filter()
                ->values();

            $duplicados = $cisApods->duplicates()->unique()->values();
            if ($duplicados->isNotEmpty()) {
                $errors['apoderados'] = 'No puede repetir el mismo CI en apoderados: ' . $duplicados->implode(', ');
            }

            // apoderado CI != estudiante CI
            if ($ciEst !== '' && $cisApods->contains($ciEst)) {
                $errors['apoderados'] = 'El CI del apoderado no puede ser igual al CI del estudiante.';
            }

            foreach (($data['apoderados'] ?? []) as $i => $ap) {
                // normaliza nombres
                foreach (['apod_nombre', 'apod_apellido_pat', 'apod_apellido_mat'] as $nf) {
                    if (isset($data['apoderados'][$i][$nf])) {
                        $data['apoderados'][$i][$nf] = $this->normalizeSpaces($data['apoderados'][$i][$nf]);
                    }
                }

                // nombres sin tildes + formato
                foreach ([
                    'apod_nombre' => 'Nombre del apoderado',
                    'apod_apellido_pat' => 'Apellido paterno del apoderado',
                    'apod_apellido_mat' => 'Apellido materno del apoderado',
                ] as $f => $lbl) {
                    $v = trim((string) ($data['apoderados'][$i][$f] ?? ''));

                    if ($f !== 'apod_apellido_mat' && $v === '') {
                        $errors["apoderados.$i.$f"] = "Debe ingresar: {$lbl}.";
                        continue;
                    }

                    if ($v !== '') {
                        if ($this->containsAccents($v)) {
                            $errors["apoderados.$i.$f"] = "{$lbl}: no use tildes/acentos (ej: José → Jose).";
                        } elseif (!$this->isValidBoliviaName($v)) {
                            $errors["apoderados.$i.$f"] = "{$lbl}: solo letras, espacios, guion o apóstrofe (sin números).";
                        }
                    }
                }

                // CI apoderado
                $ciAp = $this->normalizeCiDigits($data['apoderados'][$i]['apod_carnet_identidad'] ?? null);
                $data['apoderados'][$i]['apod_carnet_identidad'] = $ciAp;

                if ($ciAp === '' || !$this->isValidCi($ciAp)) {
                    $errors["apoderados.$i.apod_carnet_identidad"] = 'CI apoderado inválido: solo números, mínimo 8 dígitos (máx. 12).';
                } else {
                    $personaAp = Persona::where('carnet_identidad', $ciAp)->first();
                    if ($personaAp && $personaAp->habilitado === false) {
                        $errors["apoderados.$i.apod_carnet_identidad"] = 'Este CI pertenece a una persona inhabilitada. No se puede usar.';
                    }
                }

                // Fecha nacimiento apoderado: requerida + >= 18
                $fnAp = $data['apoderados'][$i]['apod_fecha_nacimiento'] ?? null;
                if (empty($fnAp)) {
                    $errors["apoderados.$i.apod_fecha_nacimiento"] = 'Debe ingresar la fecha de nacimiento del apoderado (para validar mayoría de edad).';
                } else {
                    try {
                        $dAp = Carbon::parse($fnAp)->startOfDay();
                        if ($dAp->greaterThan(Carbon::today())) {
                            $errors["apoderados.$i.apod_fecha_nacimiento"] = 'La fecha de nacimiento del apoderado no puede ser futura.';
                        } elseif ($dAp->greaterThan(Carbon::today()->subYears(18))) {
                            $errors["apoderados.$i.apod_fecha_nacimiento"] = 'El apoderado debe ser mayor de 18 años.';
                        } elseif ($dAp->lessThan(Carbon::today()->subYears(120))) {
                            $errors["apoderados.$i.apod_fecha_nacimiento"] = 'La fecha de nacimiento del apoderado parece inválida (muy antigua).';
                        }
                    } catch (\Throwable $e) {
                        $errors["apoderados.$i.apod_fecha_nacimiento"] = 'Fecha de nacimiento del apoderado inválida.';
                    }
                }

                // Teléfonos apoderado
                $tAp1 = $this->normalizeBoliviaMobile($data['apoderados'][$i]['apod_telefono_principal'] ?? null);
                $data['apoderados'][$i]['apod_telefono_principal'] = $tAp1;

                if (empty($tAp1)) {
                    $errors["apoderados.$i.apod_telefono_principal"] = 'Debe ingresar el teléfono principal del apoderado.';
                } elseif (!$this->isValidBoliviaMobile($tAp1)) {
                    $errors["apoderados.$i.apod_telefono_principal"] = 'Teléfono inválido: 8 dígitos y debe empezar con 6 o 7 (Bolivia).';
                }

                $tAp2 = $this->normalizeBoliviaMobile($data['apoderados'][$i]['apod_telefono_secundario'] ?? null);
                $data['apoderados'][$i]['apod_telefono_secundario'] = $tAp2;

                if (!empty($tAp2) && !$this->isValidBoliviaMobile($tAp2)) {
                    $errors["apoderados.$i.apod_telefono_secundario"] = 'Teléfono secundario inválido: 8 dígitos y debe empezar con 6 o 7 (Bolivia).';
                }

                // Email apoderado
                $emailAp = trim((string) ($data['apoderados'][$i]['apod_email_personal'] ?? ''));
                if ($emailAp !== '' && !filter_var($emailAp, FILTER_VALIDATE_EMAIL)) {
                    $errors["apoderados.$i.apod_email_personal"] = 'Correo del apoderado no es válido.';
                }

                // Parentesco requerido (doble seguro)
                if (empty($data['apoderados'][$i]['apod_parentesco'] ?? null)) {
                    $errors["apoderados.$i.apod_parentesco"] = 'Debe seleccionar el parentesco.';
                }

                // Principal no puede estar inhabilitado
                $esPrincipal = (bool) ($data['apoderados'][$i]['apod_es_principal'] ?? false);
                $habil = (bool) ($data['apoderados'][$i]['apod_habilitado'] ?? true);
                if ($esPrincipal && !$habil) {
                    $errors["apoderados.$i.apod_habilitado"] = 'El apoderado principal no puede estar inhabilitado.';
                }
            }
        }

        // guarda normalizaciones
        $this->data = array_merge($this->data ?? [], $data);

        if (!empty($errors)) {
            $this->failStep('No se puede continuar', 'Corrija los campos marcados para avanzar.', $errors);
        }
    }

    // =========================================================
    // VALIDACIÓN FINAL (Submit)
    // =========================================================
    protected function validateRestricciones(array &$data): void
    {
        // Aprovechamos que los Next ya validan, aquí solo hacemos un “re-chequeo” global.
        // (por si alguien intenta saltarse algo con requests manuales)

        $errors = [];

        $ci = $this->normalizeCiDigits($data['carnet_identidad'] ?? null);
        $data['carnet_identidad'] = $ci;

        if ($ci === '' || !$this->isValidCi($ci)) {
            $errors['carnet_identidad'] = 'El CI debe contener solo números y tener al menos 8 dígitos (máx. 12).';
        } else {
            if (Persona::where('carnet_identidad', $ci)->exists()) {
                $errors['carnet_identidad'] = 'Ya existe una persona registrada con este CI.';
            }
        }

        // Estado académico: en este panel NO puede quedar inscrito
        if (($data['estado_academico'] ?? null) === 'inscrito') {
            $errors['estado_academico'] = 'No puede registrar como "Inscrito" aquí. Use la página de Inscripción para inscribir.';
        }

        // Apoderados: exactamente 1 principal (doble seguro)
        $apoderados = $data['apoderados'] ?? [];
        $principalCount = collect($apoderados)->filter(fn ($a) => (bool) ($a['apod_es_principal'] ?? false))->count();
        if ($principalCount !== 1) {
            $errors['apoderados'] = 'Debe existir exactamente 1 apoderado principal.';
        }

        if (!empty($errors)) {
            $this->fail('No se puede registrar', 'Corrija los campos marcados para continuar.', $errors);
        }
    }

    // =========================================================
    // FORM
    // =========================================================

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Datos Personales')
                        ->description('Ingrese la información personal del estudiante.')
                        ->icon('heroicon-o-user')
                        ->afterValidation(fn () => $this->validateStepDatosPersonales())
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
                                        ->placeholder('Ingrese la dirección completa'),
                                ]),
                        ]),

                    Wizard\Step::make('Detalles Educativos')
                        ->icon(Heroicon::OutlinedAcademicCap)
                        ->description('Proporcione los detalles educativos del estudiante.')
                        ->afterValidation(fn () => $this->validateStepDetallesEducativos())
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
                                        ->helperText('En este panel se registra como Pendiente; la inscripción se hace en la página de Inscripción.')
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
                                ->hidden(fn (callable $get) => !$get('tiene_discapacidad'))
                                ->schema([
                                    Repeater::make('discapacidades')
                                        ->label('Lista de Discapacidades')
                                        ->schema([
                                            Select::make('discapacidad_id')
                                                ->label('Discapacidad')
                                                ->searchable()
                                                ->preload()
                                                ->options(fn () => \App\Models\Discapacidad::where('visible', true)->pluck('nombre', 'id'))
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
                                        ->itemLabel(fn (array $state): ?string =>
                                            \App\Models\Discapacidad::find($state['discapacidad_id'] ?? null)?->nombre ?? 'Nueva discapacidad'
                                        )
                                        ->columnSpanFull()
                                        ->reorderable(false),
                                ]),
                        ]),

                    // ==========================================
                    //  STEP APODERADOS (TU ESTRUCTURA ORIGINAL)
                    // ==========================================
                    Wizard\Step::make('Apoderados')
                        ->icon(Heroicon::OutlinedUserGroup)
                        ->description('Ingrese la información de los apoderados del estudiante.')
                        ->afterValidation(fn () => $this->validateStepApoderados())
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
                                        ->itemLabel(fn (array $state): ?string =>
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
                                        ->addActionLabel('Agregar Otro Apoderado')
                                        ->reorderableWithButtons()
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                    ->columnSpanFull()
                    ->submitAction(view('filament.pages.components.submit-button-estudiante'))
                    ->persistStepInQueryString()
                    ->skippable(false),
            ])
            ->statePath('data');
    }

    // =========================================================
    // CREATE
    // =========================================================

    public function create(): void
    {
        try {
            $data = $this->form->getState();

            // Asegura que también al SUBMIT se validen las reglas de cada paso
            $this->data = $data;
            $this->validateStepDatosPersonales();
            $this->validateStepDetallesEducativos();
            $this->validateStepApoderados();

            // Recupera data ya normalizada por los steps
            $data = (array) ($this->data ?? []);

            // (Opcional) Re-chequeo final si lo usas
            $this->validateRestricciones($data);

            DB::beginTransaction();

            $personaEstudiante = Persona::create([
                'nombre' => $data['nombre'],
                'apellido_pat' => $data['apellido_pat'],
                'apellido_mat' => $data['apellido_mat'] ?? null,
                'carnet_identidad' => $data['carnet_identidad'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'telefono_principal' => $data['telefono_principal'] ?? null,
                'telefono_secundario' => $data['telefono_secundario'] ?? null,
                'email_personal' => $data['email_personal'],
                'direccion' => $data['direccion'] ?? null,
                'habilitado' => $data['habilitado'] ?? true,
            ]);

            $estudiante = Estudiante::create([
                'persona_id' => $personaEstudiante->id,
                'codigo_saga' => $data['codigo_saga'],
                'estado_academico' => 'pendiente_inscripcion',
                'tiene_discapacidad' => $data['tiene_discapacidad'] ?? false,
                'observaciones' => $data['observaciones'] ?? null,
                'foto_url' => $data['foto_url'] ?? null,
            ]);

            if (!empty($data['discapacidades']) && ($data['tiene_discapacidad'] ?? false)) {
                foreach ($data['discapacidades'] as $discapacidad) {
                    if (isset($discapacidad['discapacidad_id'])) {
                        $estudiante->discapacidades()->attach(
                            $discapacidad['discapacidad_id'],
                            ['observacion' => $discapacidad['observacion'] ?? null]
                        );
                    }
                }
            }

            $apoderados = $data['apoderados'] ?? [];
            foreach ($apoderados as $apoderadoData) {
                $ciAp = preg_replace('/\D+/', '', (string) ($apoderadoData['apod_carnet_identidad'] ?? ''));

                $personaApoderado = Persona::where('carnet_identidad', $ciAp)->first();

                if ($personaApoderado && $personaApoderado->habilitado === false) {
                    $this->fail('Apoderado inhabilitado', "El CI {$ciAp} pertenece a una persona inhabilitada. No se puede usar.");
                }

                if (!$personaApoderado) {
                    $personaApoderado = Persona::create([
                        'nombre' => $apoderadoData['apod_nombre'],
                        'apellido_pat' => $apoderadoData['apod_apellido_pat'],
                        'apellido_mat' => $apoderadoData['apod_apellido_mat'] ?? null,
                        'carnet_identidad' => $ciAp,
                        'fecha_nacimiento' => $apoderadoData['apod_fecha_nacimiento'] ?? null,
                        'telefono_principal' => $this->normalizeBoliviaMobile($apoderadoData['apod_telefono_principal'] ?? null),
                        'telefono_secundario' => $this->normalizeBoliviaMobile($apoderadoData['apod_telefono_secundario'] ?? null),
                        'email_personal' => $apoderadoData['apod_email_personal'] ?? null,
                        'direccion' => $apoderadoData['apod_direccion'] ?? null,
                        'habilitado' => $apoderadoData['apod_habilitado'] ?? true,
                    ]);
                }

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

                $estudiante->apoderados()->attach($apoderado->id, [
                    'parentestco' => $apoderadoData['apod_parentesco'],
                    'vive_con_el' => $apoderadoData['apod_vive_con_el'] ?? false,
                    'es_principal' => (bool) ($apoderadoData['apod_es_principal'] ?? false),
                ]);
            }

            DB::commit();

            Notification::make()
                ->title('¡Estudiante registrado exitosamente!')
                ->success()
                ->body("El estudiante {$personaEstudiante->nombre_completo} fue registrado con el código {$estudiante->codigo_saga}.")
                ->duration(5000)
                ->send();

            $this->form->fill();
            $this->redirect(route('filament.inscripcion.resources.estudiantes.index'));
        }
        // ✅ convierte UNIQUE violation en error de campo (como los demás)
        catch (QueryException $e) {
            DB::rollBack();

            $sqlState = $e->errorInfo[0] ?? null; // en Postgres: 23505 = unique_violation
            $msg = $e->getMessage();

            if ($sqlState === '23505') {
                $errors = [];

                // Extrae el valor duplicado si viene en el mensaje
                $dupEmail = null;
                if (preg_match('/Key \(email_personal\)=\(([^)]+)\)/', $msg, $m)) {
                    $dupEmail = $m[1];
                }

                if (str_contains($msg, 'persona_email_personal_unique')) {
                    // ¿dup pertenece al estudiante?
                    if ($dupEmail && (($this->data['email_personal'] ?? null) === $dupEmail)) {
                        $errors['email_personal'] = 'Este correo ya está registrado en otra persona.';
                    } else {
                        // busca en apoderados cuál fue
                        foreach (($this->data['apoderados'] ?? []) as $i => $ap) {
                            if (($ap['apod_email_personal'] ?? null) === $dupEmail) {
                                $errors["apoderados.$i.apod_email_personal"] = 'Este correo ya está registrado en otra persona.';
                            }
                        }
                    }

                    // fallback por si no pudo detectar
                    if (empty($errors)) {
                        $errors['email_personal'] = 'El correo ya está registrado en otra persona.';
                    }

                    $this->fail('No se puede registrar', 'Corrija los campos marcados para continuar.', $errors);
                }

                // otras uniques (por si acaso)
                $this->fail('No se puede registrar', 'Hay datos duplicados (restricción de unicidad). Revise los campos.', [
                    '_db' => 'Existe un registro con datos duplicados. Revise CI / correo.',
                ]);
            }

            // si no es 23505, muestra genérico sin SQL
            Notification::make()
                ->title('Error al registrar estudiante')
                ->danger()
                ->body('Ocurrió un error inesperado al guardar.')
                ->persistent()
                ->send();
        }
        catch (ValidationException $e) {
            throw $e;
        }
        catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error al registrar estudiante')
                ->danger()
                ->body('Ocurrió un error inesperado.')
                ->persistent()
                ->send();

            \Log::error('Error al crear estudiante:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function getSubheading(): ?string
    {
        return 'Complete el formulario para registrar un estudiante (queda como Pendiente de Inscripción) y sus apoderados.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_students')
                ->label('Ver estudiantes')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(route('filament.inscripcion.resources.estudiantes.index')),
        ];
    }

    public function getFooter(): ?View
    {
        return view('filament.pages.footer-inscripcion-avanzada');
    }
}
