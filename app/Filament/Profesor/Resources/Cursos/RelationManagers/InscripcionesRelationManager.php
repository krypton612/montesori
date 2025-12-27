<?php

namespace App\Filament\Profesor\Resources\Cursos\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InscripcionesRelationManager extends RelationManager
{
    protected static string $relationship = 'inscripciones';

    protected static ?string $title = 'Estudiantes Inscritos';

    protected static string|null|\BackedEnum $icon = Heroicon::UserGroup;


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo_inscripcion')
                    ->label('Código de Inscripción')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                    
                Select::make('estudiante_id')
                    ->label('Estudiante')
                    ->relationship('estudiante.persona', 'nombre_completo')
                    ->searchable(['nombre', 'apellido_pat', 'apellido_mat'])
                    ->preload()
                    ->required(),
                    
                Select::make('grupo_id')
                    ->label('Grupo')
                    ->relationship('grupo', 'nombre')
                    ->searchable()
                    ->preload(),
                    
                Select::make('gestion_id')
                    ->label('Gestión')
                    ->relationship('gestion', 'nombre')
                    ->required()
                    ->preload(),
                    
                DatePicker::make('fecha_inscripcion')
                    ->label('Fecha de Inscripción')
                    ->required()
                    ->default(now())
                    ->native(false),
                    
                Select::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'nombre')
                    ->required()
                    ->preload(),
                    
                TextInput::make('condiciones')
                    ->label('Condiciones Especiales')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('codigo_inscripcion')
            ->columns([
                Split::make([
                    // Columna izquierda: Foto y datos del estudiante
                    Stack::make([
                        Split::make([
                            ImageColumn::make('estudiante.foto_url')
                                ->disk('public')
                                ->label('Foto')
                                ->circular()
                                ->defaultImageUrl(url('/images/default-avatar.png'))
                                ->size(50)
                                ->extraAttributes(['class' => 'ring-2 ring-primary-500']),
                                
                            Stack::make([
                                TextColumn::make('estudiante.persona.nombre_completo')
                                    ->label('Estudiante')
                                    ->searchable(['persona.nombre', 'persona.apellido_pat', 'persona.apellido_mat'])
                                    ->sortable(['persona.nombre'])
                                    ->weight('bold')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('primary'),
                                    
                                TextColumn::make('estudiante.codigo_saga')
                                    ->label('SAGA')
                                    ->searchable()
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-hashtag'),
                            ])->space(1),
                        ]),
                    ])->space(2),
                    
                    // Columna central: Información de inscripción
                    Stack::make([
                        TextColumn::make('codigo_inscripcion')
                            ->label('Código')
                            ->searchable()
                            ->sortable()
                            ->copyable()
                            ->copyMessage('Código copiado')
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-o-document-text')
                            ->weight('semibold'),
                            
                        Split::make([
                            TextColumn::make('grupo.nombre')
                                ->label('Grupo')
                                ->badge()
                                ->color('purple')
                                ->icon('heroicon-o-user-group')
                                ->placeholder('Sin grupo')
                                ->sortable(),
                                
                            TextColumn::make('gestion.nombre')
                                ->label('Gestión')
                                ->badge()
                                ->color('indigo')
                                ->icon('heroicon-o-calendar-days')
                                ->sortable(),
                        ]),
                    ])->space(2),
                    
                    // Columna derecha: Estado y fecha
                    Stack::make([
                        TextColumn::make('estado.nombre')
                            ->label('Estado')
                            ->badge()
                            ->color(fn ($record) => match($record->estado?->nombre) {
                                'Activo', 'Inscrito' => 'success',
                                'Pendiente' => 'warning',
                                'Retirado', 'Cancelado' => 'danger',
                                'Completado' => 'info',
                                default => 'gray',
                            })
                            ->icon(fn ($record) => match($record->estado?->nombre) {
                                'Activo', 'Inscrito' => 'heroicon-o-check-circle',
                                'Pendiente' => 'heroicon-o-clock',
                                'Retirado', 'Cancelado' => 'heroicon-o-x-circle',
                                'Completado' => 'heroicon-o-academic-cap',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->sortable()
                            ->alignCenter(),
                            
                        TextColumn::make('fecha_inscripcion')
                            ->label('Fecha')
                            ->date('d/M/Y')
                            ->sortable()
                            ->since()
                            ->description(fn ($record): string => $record->fecha_inscripcion->format('d/m/Y'))
                            ->icon('heroicon-o-calendar')
                            ->iconColor('success')
                            ->alignCenter(),
                    ])->space(2)->alignment('end'),
                ])->from('md'),
                
                // Columnas adicionales toggleables
                TextColumn::make('estudiante.persona.telefono_principal')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->iconColor('green')
                    ->copyable()
                    ->copyMessage('Teléfono copiado')
                    ->placeholder('Sin teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('estudiante.persona.email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->iconColor('blue')
                    ->copyable()
                    ->copyMessage('Email copiado')
                    ->placeholder('Sin email')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),
                    
                
                    
                TextColumn::make('documentos_count')
                    ->label('Documentos')
                    ->counts('documentos')
                    ->badge()
                    ->color('cyan')
                    ->icon('heroicon-o-document-duplicate')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip('Documentos adjuntos')
                    ->toggleable(),
                    
               
            ])
            ->defaultSort('fecha_inscripcion', 'desc')
            ->filters([
                SelectFilter::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'nombre')
                    ->multiple()
                    ->preload()
                    ->native(false)
                    ->indicator('Estado'),
                    
                SelectFilter::make('grupo_id')
                    ->label('Grupo')
                    ->relationship('grupo', 'nombre')
                    ->multiple()
                    ->preload()
                    ->native(false)
                    ->indicator('Grupo'),
                    
                SelectFilter::make('gestion_id')
                    ->label('Gestión')
                    ->relationship('gestion', 'nombre')
                    ->preload()
                    ->native(false)
                    ->indicator('Gestión'),
            ])
            ->headerActions([
                Action::make('Imprimir Lista de Inscritos')
                    ->icon(Heroicon::Printer)
                    ->action(fn (RelationManager $livewire) => $livewire->redirectToCreateForm())
                    ->button(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                ])
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
                    ->label('Acciones')
            ])
            ->toolbarActions([

            ])
            ->emptyStateHeading('No hay estudiantes inscritos')
            ->emptyStateDescription('Comienza inscribiendo estudiantes a este curso.')
            ->emptyStateIcon('heroicon-o-user-plus')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s')
            ->deferLoading();
    }
}