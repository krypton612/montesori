<?php

namespace App\Filament\Profesor\Pages;

use App\Models\Curso;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class RegistroNotas extends Page implements HasForms, HasTable
{

    use InteractsWithForms;
    use InteractsWithTable;

    protected string $view = 'filament.profesor.pages.registro-notas';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión Académica';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowUp;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::DocumentArrowUp;


    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'estudiante_id' => null,
        ]);
    }

    // para el formulario de seleccion de grupo o curso irregular
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Grupo o Curso Irregular')
                    ->description('Seleccione el grupo o curso irregular a evaluar, reviese el manual de evaluacion.')
                    ->icon(Heroicon::OutlinedMagnifyingGlass)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('grupo_id')
                                    ->label('Grupo o Curso Irregular')
                                    ->options(function () {
                                        $usuario = auth()->user();
                                        $options = [];

                                        try {
                                            if (!$usuario->persona || !$usuario->persona->profesor) {
                                                return $options;
                                            }
                                        } catch (\Exception $e) {
                                            return $options;
                                        }
                                        $profesor = $usuario->persona->profesor;

                                        $profesor->cursos()->with('grupos')->get()->each(function ($curso) use (&$options) {
                                            if ($curso->grupos->isEmpty()) {
                                                // Curso sin grupo (irregular)
                                                $options['curso_'.$curso->id] = $curso->materia->nombre . ' - ' . $curso->seccion . ' (Irregular)'; 
                                            } else {
                                                // Cursos con grupos
                                                foreach ($curso->grupos as $grupo) {
                                                    $options[$grupo->id] = $grupo->codigo . ' - ' . $grupo->nombre;
                                                }
                                            }
                                        });

                                        return $options;
                                    })
                                    ->searchable()
                                    ->required(),
                                Placeholder::make('info')
                                    ->content('Después de seleccionar el grupo o curso irregular, la tabla de notas se actualizará automáticamente para mostrar los estudiantes correspondientes.'),
                            ])
                    ]),  
            ]);
    }

    // 
    protected function getTableQuery(): Builder
    {
        $estudianteId = $this->data['estudiante_id'] ?? null;
        
        return \App\Models\NotaEstudiante::query()
            ->when($estudianteId, function (Builder $query) use ($estudianteId) {
                $query->where('estudiante_id', $estudianteId);
            });
    }

    // para la tabla de notas registradas + 'estudiante' | 'curso' | 'nota' | 'fecha_registro'
    public function table(Table $table): Table
    {
        return $table
            ->columns([

            ]);
    }

    public function getSubheading(): ?string
    {
        return 'Este modulo permite registrar las notas de los estudiantes, revise las fechas de habilitación de cargado de notas individuales. Revise el manual haciendo clic en el botón a la derecha.';
    }
}
