<?php

namespace App\Filament\Inscripcion\Pages;

use App\Models\Estudiante;
use App\Models\Inscripcion;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KardexEstudiante extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected string $view = 'filament.pages.kardex-estudiante';

    protected static string|null|\UnitEnum $navigationGroup = 'Consultas';
    protected static ?string $navigationLabel = 'Kardex Estudiante';
    protected static ?string $title = 'Kardex del Estudiante';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['estudiante_id' => null]);
    }

    protected function getHeaderActions(): array
    {
        $getId = fn () => $this->data['estudiante_id'] ?? null;

        return [
            Action::make('preview_pdf')
                ->label('Vista previa PDF')
                ->icon(Heroicon::OutlinedEye)
                ->visible(fn () => filled($getId()))
                ->modalHeading('Vista previa - Kardex (Estudiante)')
                ->modalContent(fn () => view('modals.preview-document', [
                    'url' => route('documents.kardex_estudiante.preview', $getId()),
                ]))
                ->action(fn () => null),

            Action::make('download_pdf')
                ->label('Descargar PDF')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->visible(fn () => filled($getId()))
                ->url(fn () => route('documents.kardex_estudiante.download', $getId()))
                ->openUrlInNewTab(),
        ];
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Consulta')
                    ->description('Seleccione un estudiante para ver su kardex completo.')
                    ->icon(Heroicon::OutlinedMagnifyingGlass)
                    ->schema([
                        Select::make('estudiante_id')
                            ->label('Estudiante')
                            ->searchable()
                            ->preload(false)
                            ->native(false)
                            ->required()
                            ->getSearchResultsUsing(function (string $search): array {
                                $search = trim($search);

                                return Estudiante::query()
                                    ->with('persona')
                                    ->where('codigo_saga', 'ilike', "%{$search}%")
                                    ->orWhereHas('persona', function ($q) use ($search) {
                                        $q->where('nombre', 'ilike', "%{$search}%")
                                            ->orWhere('apellido_pat', 'ilike', "%{$search}%")
                                            ->orWhere('apellido_mat', 'ilike', "%{$search}%")
                                            ->orWhere('carnet_identidad', 'ilike', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($e) {
                                        $p = $e->persona;
                                        $nombre = $p
                                            ? trim(($p->nombre ?? '') . ' ' . ($p->apellido_pat ?? '') . ' ' . ($p->apellido_mat ?? ''))
                                            : 'Sin persona';

                                        $ci = $p?->carnet_identidad ?? 'S/C';
                                        $saga = $e->codigo_saga ?? 'S/SAGA';

                                        return [$e->id => "{$nombre} ({$saga}) - CI: {$ci}"];
                                    })
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                if (!$value) return null;

                                $e = Estudiante::with('persona')->find($value);
                                if (!$e) return null;

                                $p = $e->persona;
                                $nombre = $p
                                    ? trim(($p->nombre ?? '') . ' ' . ($p->apellido_pat ?? '') . ' ' . ($p->apellido_mat ?? ''))
                                    : 'Sin persona';

                                $ci = $p?->carnet_identidad ?? 'S/C';
                                $saga = $e->codigo_saga ?? 'S/SAGA';

                                return "{$nombre} ({$saga}) - CI: {$ci}";
                            })
                            ->live()
                            ->afterStateUpdated(fn () => $this->resetTable())
                            ->helperText('Puede buscar por nombre, CI o código SAGA.')
                            ->columnSpanFull(),

                        /* =============================
                           RESUMEN DEL ESTUDIANTE (MEJORADO)
                           ============================= */
                        Section::make('Datos del estudiante')
                            ->description('Información general del estudiante.')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->schema([
                                Grid::make(4)->schema([
                                    Placeholder::make('nombre_completo')
                                        ->label('Nombre completo')
                                        ->icon(Heroicon::OutlinedUser)
                                        ->content(fn (Get $get) =>
                                            $this->getEstudiantePersona($get('estudiante_id'))?->nombre_completo ?? '—'
                                        ),

                                    Placeholder::make('ci')
                                        ->label('CI')
                                        ->icon(Heroicon::OutlinedIdentification)
                                        ->content(fn (Get $get) =>
                                            $this->getEstudiantePersona($get('estudiante_id'))?->carnet_identidad ?? '—'
                                        ),

                                    Placeholder::make('saga')
                                        ->label('Código SAGA')
                                        ->icon(Heroicon::OutlinedHashtag)
                                        ->content(fn (Get $get) =>
                                            Estudiante::find($get('estudiante_id'))?->codigo_saga ?? '—'
                                        ),

                                    Placeholder::make('edad')
                                        ->label('Edad')
                                        ->icon(Heroicon::OutlinedCake)
                                        ->content(function (Get $get) {
                                            $p = $this->getEstudiantePersona($get('estudiante_id'));
                                            return $p ? "{$p->calcularEdad()} años" : '—';
                                        }),
                                ]),

                                Grid::make(4)->schema([
                                    Placeholder::make('direccion')
                                        ->label('Dirección')
                                        ->icon(Heroicon::OutlinedMapPin)
                                        ->content(fn (Get $get) =>
                                            $this->getEstudiantePersona($get('estudiante_id'))?->direccion ?? '—'
                                        ),

                                    Placeholder::make('telefono_principal')
                                        ->label('Teléfono principal')
                                        ->icon(Heroicon::OutlinedPhone)
                                        ->content(fn (Get $get) =>
                                            $this->getEstudiantePersona($get('estudiante_id'))?->telefono_principal ?? '—'
                                        ),

                                    Placeholder::make('telefono_secundario')
                                        ->label('Teléfono secundario')
                                        ->icon(Heroicon::OutlinedDevicePhoneMobile)
                                        ->content(fn (Get $get) =>
                                            $this->getEstudiantePersona($get('estudiante_id'))?->telefono_secundario ?? '—'
                                        ),

                                    Placeholder::make('email')
                                        ->label('Correo electrónico')
                                        ->icon(Heroicon::OutlinedEnvelope)
                                        ->content(fn (Get $get) =>
                                            $this->getEstudiantePersona($get('estudiante_id'))?->email_personal ?? '—'
                                        ),
                                ]),
                                Placeholder::make('discapacidad')
                                    ->label('Discapacidad(es)')
                                    ->icon(Heroicon::OutlinedExclamationTriangle)
                                    ->content(function (Get $get) {
                                        $id = $get('estudiante_id');
                                        if (!$id) return '—';

                                        $e = Estudiante::with('discapacidades')->find($id);
                                        if (!$e || $e->discapacidades->isEmpty()) {
                                            return 'No registra discapacidad';
                                        }

                                        return $e->discapacidades
                                            ->map(fn ($d) => "🟡 {$d->nombre}")
                                            ->implode(' | ');
                                    }),
                            ])
                            ->collapsible()
                            ->collapsed(false),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getEstudiantePersona($id)
    {
        if (!$id) return null;
        $e = Estudiante::with('persona')->find($id);
        return $e?->persona;
    }

    protected function getTableQuery(): Builder
    {
        $estudianteId = $this->data['estudiante_id'] ?? null;

        if (!$estudianteId || !Estudiante::whereKey($estudianteId)->exists()) {
            return Inscripcion::query()->whereRaw('1=0');
        }

        return Inscripcion::query()
            ->where('estudiante_id', $estudianteId)
            ->with(['grupo.gestion'])
            ->latest('id');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo_inscripcion')
                    ->label('Código')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grupo.nombre')
                    ->label('Grupo')
                    ->icon(Heroicon::OutlinedUsers)
                    ->sortable(),

                TextColumn::make('grupo.gestion.nombre')
                    ->label('Gestión')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha de inscripción')
                    ->icon(Heroicon::OutlinedCalendar)
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->striped()
            ->deferLoading()
            ->emptyStateHeading('Sin historial de inscripciones')
            ->emptyStateDescription('Seleccione un estudiante para ver su historial.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public function getSubheading(): ?string
    {
        return 'Consulta completa del estudiante (datos personales, contacto, discapacidad e historial).';
    }
}
