<?php

namespace App\Filament\Inscripcion\Pages;

use App\Models\Apoderado;
use App\Models\Estudiante;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use Filament\Tables;

class KardexApoderados extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use HasPageShield;

    protected string $view = 'filament.pages.kardex-apoderados';

    protected static string|null|\UnitEnum $navigationGroup = 'Consultas';
    protected static ?string $navigationLabel = 'Kardex + Apoderados';
    protected static ?string $title = 'Kardex de Apoderados';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'estudiante_id' => null,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Consulta')
                    ->description('Seleccione un estudiante para ver su kardex de apoderados.')
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
                            ->afterStateUpdated(function () {
                                // refresca la tabla cuando cambias el estudiante
                                $this->resetTable();
                            })
                            ->helperText('Puede buscar por nombre, CI o código SAGA.')
                            ->columnSpanFull(),

                        Grid::make(4)
                            ->schema([
                                Placeholder::make('estudiante_nombre')
                                    ->label('Nombre')
                                    ->content(function (Get $get) {
                                        $id = $get('estudiante_id');
                                        if (!$id) return '—';

                                        $e = Estudiante::with('persona')->find($id);
                                        $p = $e?->persona;

                                        return $p
                                            ? trim(($p->nombre ?? '') . ' ' . ($p->apellido_pat ?? '') . ' ' . ($p->apellido_mat ?? ''))
                                            : '—';
                                    }),

                                Placeholder::make('estudiante_ci')
                                    ->label('CI')
                                    ->content(function (Get $get) {
                                        $id = $get('estudiante_id');
                                        if (!$id) return '—';

                                        $e = Estudiante::with('persona')->find($id);
                                        return $e?->persona?->carnet_identidad ?? '—';
                                    }),

                                Placeholder::make('estudiante_saga')
                                    ->label('Código SAGA')
                                    ->content(function (Get $get) {
                                        $id = $get('estudiante_id');
                                        if (!$id) return '—';

                                        return Estudiante::find($id)?->codigo_saga ?? '—';
                                    }),

                                Placeholder::make('apoderados_count')
                                    ->label('Apoderados')
                                    ->content(function (Get $get) {
                                        $id = $get('estudiante_id');
                                        if (!$id) return '—';

                                        $e = Estudiante::find($id);
                                        if (!$e) return '—';

                                        return (string) $e->apoderados()->count();
                                    }),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }
    protected function getHeaderActions(): array
    {
        $getId = fn () => $this->data['estudiante_id'] ?? null;

        return [
            Action::make('preview_pdf')
                ->label('Vista previa PDF')
                ->icon(Heroicon::OutlinedEye)
                ->visible(fn () => filled($getId()))
                ->modalHeading('Vista previa - Kardex + Apoderados')
                ->modalWidth('7xl')
                ->modalContent(fn () => view('modals.preview-document', [
                    'url' => route('documents.kardex_apoderados.preview', $getId()),
                ]))
                ->action(fn () => null),

            Action::make('download_pdf')
                ->label('Descargar PDF')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->visible(fn () => filled($getId()))
                ->url(fn () => route('documents.kardex_apoderados.download', $getId())),
        ];
    }

    /**
     * Tabla: lista de apoderados DEL estudiante seleccionado.
     * Incluye campos del pivot como columnas "planas":
     * - parentestco, vive_con_el, es_principal
     */
    protected function getTableQuery(): Builder
    {
        $estudianteId = $this->data['estudiante_id'] ?? null;

        // Sin estudiante: tabla vacía
        if (!$estudianteId) {
            return Apoderado::query()->whereRaw('1=0');
        }

        // Si no existe: tabla vacía
        if (!Estudiante::whereKey($estudianteId)->exists()) {
            return Apoderado::query()->whereRaw('1=0');
        }

        // Tomamos el nombre REAL del pivot desde la relación (no asumimos nombres)
        $relation = (new Estudiante())->apoderados(); // BelongsToMany
        $pivotTable = $relation->getTable();          // ej: apoderado_estudiante
        $fkPivot    = $relation->getForeignPivotKeyName(); // ej: estudiante_id
        $rkPivot    = $relation->getRelatedPivotKeyName(); // ej: apoderado_id

        $apoderadoTable = (new Apoderado())->getTable();

        return Apoderado::query()
            ->select("{$apoderadoTable}.*")
            ->addSelect([
                'parentestco' => "{$pivotTable}.parentestco",
                'vive_con_el' => "{$pivotTable}.vive_con_el",
                'es_principal' => "{$pivotTable}.es_principal",
            ])
            ->join($pivotTable, "{$pivotTable}.{$rkPivot}", '=', "{$apoderadoTable}.id")
            ->where("{$pivotTable}.{$fkPivot}", $estudianteId)
            ->with('persona');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('persona_nombre')
                    ->label('Apoderado')
                    ->state(function (Apoderado $record) {
                        $p = $record->persona;
                        return $p
                            ? trim(($p->nombre ?? '') . ' ' . ($p->apellido_pat ?? '') . ' ' . ($p->apellido_mat ?? ''))
                            : 'Sin persona';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('persona', function ($q) use ($search) {
                            $q->where('nombre', 'ilike', "%{$search}%")
                                ->orWhere('apellido_pat', 'ilike', "%{$search}%")
                                ->orWhere('apellido_mat', 'ilike', "%{$search}%");
                        });
                    })
                    ->sortable(),

                TextColumn::make('persona.carnet_identidad')
                    ->label('CI')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('parentestco')
                    ->label('Parentesco')
                    ->badge()
                    ->sortable()
                    ->placeholder('—'),

                IconColumn::make('vive_con_el')
                    ->label('Vive con él')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('es_principal')
                    ->label('Principal')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('persona.telefono_principal')
                    ->label('Teléfono')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('persona.email_personal')
                    ->label('Email')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('es_principal', 'desc')
            ->striped()
            ->deferLoading()
            ->emptyStateHeading('Sin resultados')
            ->emptyStateDescription('Seleccione un estudiante para ver sus apoderados, o el estudiante aún no tiene apoderados.')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public function getSubheading(): ?string
    {
        return 'Consulta rápida del estudiante y su relación con apoderados (principal, parentesco y convivencia).';
    }
}
