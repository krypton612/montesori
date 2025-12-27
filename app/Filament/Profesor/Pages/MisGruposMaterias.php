<?php

namespace App\Filament\Profesor\Pages;

use App\Models\Grupo;
use BackedEnum;
use Filament\Actions\Action as ActionsAction;
use Filament\Pages\Page;
use Filament\Resources\Concerns\HasTabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MisGruposMaterias extends Page implements HasTable
{
    use InteractsWithTable;
    use HasTabs;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    
    protected string $view = 'filament.profesor.pages.mis-grupos-materias';
    
    protected static ?string $navigationLabel = 'Mis Grupos y Materias';
    
    protected static ?string $title = 'Mis Grupos y Materias';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión Académica';
    
    protected static ?int $navigationSort = 1;

    public function mount(): void
    {
        if (!Auth::user()->persona?->profesor) {
            abort(403, 'No tienes acceso como profesor');
        }
    }

    public function table(Table $table): Table
    {
        $profesor = Auth::user()->persona->profesor;

        return $table
            ->query(
                Grupo::query()
                    ->whereHas('cursos', function ($query) use ($profesor) {
                        $query->where('profesor_id', $profesor->id);
                    })
                    ->withCount(['cursos' => function ($query) use ($profesor) {
                        $query->where('profesor_id', $profesor->id);
                    }])
            )
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-hashtag')
                    ->copyable()
                    ->copyMessage('Código copiado'),

                TextColumn::make('nombre')
                    ->label('Nombre del Grupo')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Grupo $record): string => $record->descripcion ?? '')
                    ->wrap(),

                TextColumn::make('cursos_count')
                    ->label('Cursos')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                TextColumn::make('materias_count')
                    ->label('Materias')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->getStateUsing(function (Grupo $record) use ($profesor) {
                        return $record->cursos()
                            ->where('profesor_id', $profesor->id)
                            ->distinct('materia_id')
                            ->count('materia_id');
                    }),

                TextColumn::make('gestion.nombre')
                    ->label('Gestión')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('activo')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo'),
            ])
            ->filters([
                SelectFilter::make('gestion_id')
                    ->label('Gestión')
                    ->relationship('gestion', 'nombre')
                    ->preload(),

                SelectFilter::make('activo')
                    ->label('Estado')
                    ->options([
                        '1' => 'Activo',
                        '0' => 'Inactivo',
                    ]),
            ])
            ->actions([
                ActionsAction::make('ver_materias')
                    ->label('Ver Materias')
                    ->icon('heroicon-o-book-open')
                    ->color('primary')
                    ->modalHeading(fn (Grupo $record) => "Materias del grupo: {$record->nombre}")
                    ->modalContent(fn (Grupo $record) => view('filament.profesor.modals.tabla-materias', [
                        'grupoId' => $record->id,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                ActionsAction::make('ver_cursos')
                    ->label('Ver Cursos')
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('info')
                    ->url(fn (Grupo $record): string => route('filament.profesor.resources.cursos.index', [
                        'tableFilters' => ['grupo_id' => ['value' => $record->id]]
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('nombre')
            ->paginated([10, 25, 50])
            ->poll('30s')
            ->emptyStateHeading('No tienes grupos asignados')
            ->emptyStateDescription('Actualmente no tienes grupos con cursos asignados.')
            ->emptyStateIcon('heroicon-o-folder-open');
    }

    protected function getMateriasDelGrupo(Grupo $grupo): \Illuminate\Support\Collection
    {
        $profesor = Auth::user()->persona->profesor;

        return \App\Models\Materia::whereHas('cursos', function ($query) use ($profesor, $grupo) {
            $query->where('profesor_id', $profesor->id)
                  ->whereHas('grupos', function ($q) use ($grupo) {
                      $q->where('grupo.id', $grupo->id);
                  });
        })
        ->with('cursos', function ($query) use ($profesor, $grupo) {
            $query->where('profesor_id', $profesor->id)
                  ->whereHas('grupos', function ($q) use ($grupo) {
                      $q->where('grupo.id', $grupo->id);
                  });
        })
        ->get();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos los Grupos'),
            'active' => Tab::make('Grupos Activos'),
            'inactive' => Tab::make('Grupos Inactivos'),
        ];
    }
    protected function getActiveTab(): string
    {
        return $this->activeTab ?? $this->getDefaultActiveTab() ?? 'all';
    }

    public function getDefaultActiveTab(): string
    {
        return 'all';
    }
}