<?php

namespace App\Filament\Resources\Cursos\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EvaluacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'evaluaciones';

    protected static ?string $title = 'Plan de Evaluaciones';

    protected static string|null|\BackedEnum $icon = 'heroicon-o-clipboard-document-check';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Información General')
                    ->icon('heroicon-o-information-circle')
                    ->description('Datos básicos de la evaluación')
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Primera Evaluación Parcial')
                            ->columnSpanFull(),

                        Select::make('tipo_evaluacion_id')
                            ->label('Tipo de Evaluación')
                            ->relationship('tipoEvaluacion', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->disabled(fn ($livewire) => $livewire instanceof EditAction),

                        Select::make('estado_id')
                            ->label('Estado')
                            ->relationship('estado', 'nombre', fn ($query) => $query->where('tipo', 'evaluaciones'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Describe los temas o contenidos a evaluar...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Fechas y Configuración')
                    ->icon('heroicon-o-calendar')
                    ->description('Periodo y visibilidad de la evaluación')
                    ->schema([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->closeOnDateSelection(),

                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->closeOnDateSelection()
                            ->after('fecha_inicio'),

                        Toggle::make('visible')
                            ->label('Visible para Estudiantes')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Determina si los estudiantes pueden ver esta evaluación'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->defaultSort('fecha_inicio', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título de la Evaluación')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->size('sm')
                    ->wrap()
                    ->icon('heroicon-m-document-text')
                    ->iconColor('primary')
                    ->description(fn ($record) => $record->descripcion ? \Illuminate\Support\Str::limit($record->descripcion, 50) : null),

                TextColumn::make('tipoEvaluacion.nombre')
                    ->label('Tipo')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Parcial' => 'info',
                        'Final' => 'danger',
                        'Trabajo Práctico' => 'warning',
                        'Laboratorio' => 'success',
                        'Tarea' => 'gray',
                        default => 'primary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Parcial' => 'heroicon-m-academic-cap',
                        'Final' => 'heroicon-m-trophy',
                        'Trabajo Práctico' => 'heroicon-m-document-text',
                        'Laboratorio' => 'heroicon-m-beaker',
                        'Tarea' => 'heroicon-m-clipboard-document-list',
                        default => 'heroicon-m-clipboard-document-check',
                    }),

                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->iconColor('success')
                    ->size('sm')
                    ->tooltip(fn ($record) => 'Inicia: ' . $record->fecha_inicio->format('d/m/Y')),

                TextColumn::make('fecha_fin')
                    ->label('Finalización')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->iconColor('danger')
                    ->size('sm')
                    ->tooltip(fn ($record) => 'Finaliza: ' . $record->fecha_fin->format('d/m/Y'))
                    ->color(fn ($record) => $record->fecha_fin->isPast() ? 'danger' : 'success'),

                TextColumn::make('duracion')
                    ->label('Duración')
                    ->getStateUsing(fn ($record) => $record->fecha_inicio->diffInDays($record->fecha_fin) . ' días')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-m-clock'),

                TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activa' => 'success',
                        'Pendiente' => 'warning',
                        'Finalizada' => 'danger',
                        'Cancelada' => 'gray',
                        default => 'primary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Activa' => 'heroicon-m-check-circle',
                        'Pendiente' => 'heroicon-m-clock',
                        'Finalizada' => 'heroicon-m-check-badge',
                        'Cancelada' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-information-circle',
                    }),

                IconColumn::make('visible')
                    ->label('Visible')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->visible ? 'Visible para estudiantes' : 'Oculto para estudiantes'),

                TextColumn::make('gestion.nombre')
                    ->label('Gestión')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-calendar-days')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo_evaluacion_id')
                    ->label('Tipo de Evaluación')
                    ->relationship('tipoEvaluacion', 'nombre')
                    ->multiple()
                    ->preload()
                    ->indicator('Tipo'),

                SelectFilter::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'nombre')
                    ->multiple()
                    ->preload()
                    ->indicator('Estado'),

                SelectFilter::make('visible')
                    ->label('Visibilidad')
                    ->options([
                        1 => 'Visible',
                        0 => 'Oculto',
                    ])
                    ->indicator('Visibilidad'),
            ])
            ->headerActions([

            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Acciones'),
            ])
            ->bulkActions([

            ])
            ->emptyStateHeading('No hay evaluaciones registradas')
            ->emptyStateDescription('Crea la primera evaluación para este curso.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->emptyStateActions([

            ])
            ->paginated([10, 25, 50, 100])
            ->poll('30s')
            ->deferLoading();
    }

    protected function getListeners(): array
    {
        return [
            'parentUpdated' => '$refresh',
        ];
    }

    public static function getTitle($ownerRecord = null, $pageClass = null): string
    {
        return 'Plan de Evaluaciones';
    }
}
