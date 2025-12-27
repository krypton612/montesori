<?php

namespace App\Livewire\Profesor;

use App\Models\Curso;
use Dom\Text;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use SebastianBergmann\CodeCoverage\Filter;

class MateriasGrupoTable extends Component implements HasTable, HasForms, HasSchemas
{
    use InteractsWithTable;

    use InteractsWithActions;       // ← NUEVO y obligatorio para mountedActions
    use InteractsWithSchemas;

    public int $grupoId;  
    

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Curso::query()
                ->with(['materia'])
                ->where('profesor_id', auth()->user()?->persona?->profesor?->id ?? 0)
                ->whereHas('grupos', function ($query) {
                    $query->where('grupo.id', $this->grupoId);
                })
                )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->icon(Heroicon::AcademicCap)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('seccion')
                    ->label('Codigo Curso')
                    ->icon(Heroicon::AcademicCap)
                    ,

                TextColumn::make('materia.nombre')
                    ->label('Materia')
                    ->description('Materia asignada al curso')
                    ->badge()
                    ->wrap(),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->wrap(),
                TextColumn::make('horas_semanales')
                    ->label('H/S Minimas'),
                TextColumn::make('materia.grado')
                    ->label('Grado'),
                TextColumn::make('materia.nivel')
                    ->label('Complejidad'),
                IconColumn::make('materia.habilitado')
                    ->label('Habilitado')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('materia.nivel')
                    ->label('Complejidad')
                    ->options([
                        '1' => 'Muy Fácil',
                        '2' => 'Fácil',
                        '3' => 'Intermedio',
                        '4' => 'Difícil',
                        '5' => 'Muy Difícil',
                        '6' => 'Extremo',
                    ])
                    // probable SQLI | OJO
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value']) {
                            // Filtramos manualmente usando la relación sin cargar todo
                            $query->whereHas('materia', function ($q) use ($data) {
                                $q->where('nivel', $data['value']);
                            });
                        }
                        return $query;
                    }),
                
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.profesor.materias-grupo-table');
    }
}
