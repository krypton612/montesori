<?php

namespace App\Filament\Resources\Estudiantes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EstudianteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->description('Selecciona la persona asociada a este estudiante')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        Select::make('persona_id')
                            ->label('Persona')
                            ->preload()
                            ->searchable(['nombre', 'apellido_pat', 'apellido_mat', /*'ci'*/])
                            ->relationship(
                                'persona',
                                'nombre',
                                fn (Builder $query, $operation) =>
                                    // Excluir personas que ya tienen un estudiante asignado (excepto en edición)
                                $operation === 'create'
                                    ? $query->whereDoesntHave('estudiante')
                                    : $query
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) =>
                                "{$record->nombre} {$record->apellido_pat} {$record->apellido_mat}" .
                                ($record->carnet_identidad ? " - CI: {$record->carnet_identidad}" : "")
                            )
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-user-circle')
                            ->helperText('Selecciona una persona que aún no tenga un registro de estudiante')
                            ->placeholder('Busca por nombre, apellido o CI')
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $persona = \App\Models\Persona::find($state);
                                    if ($persona && $persona->foto_url) {
                                        $set('foto_url', $persona->foto_url);
                                    }
                                }
                            })
                            ->createOptionForm([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ej: Juan')
                                            ->prefixIcon('heroicon-o-user'),

                                        TextInput::make('apellido_pat')
                                            ->label('Apellido Paterno')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ej: Pérez'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('apellido_mat')
                                            ->label('Apellido Materno')
                                            ->maxLength(255)
                                            ->placeholder('Ej: García'),

                                        TextInput::make('ci')
                                            ->label('Carnet de Identidad')
                                            ->maxLength(20)
                                            ->unique('persona', 'ci')
                                            ->placeholder('Ej: 12345678')
                                            ->prefixIcon('heroicon-o-identification'),
                                    ]),

                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('correo@ejemplo.com')
                                    ->prefixIcon('heroicon-o-envelope'),

                                TextInput::make('telefono')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('+591 12345678')
                                    ->prefixIcon('heroicon-o-phone'),
                            ]),
                    ]),

                Section::make('Información Académica')
                    ->description('Datos relacionados con el registro académico del estudiante')
                    ->icon('heroicon-o-academic-cap')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('codigo_saga')
                                    ->label('Código SAGA')
                                    ->maxLength(50)
                                    ->placeholder('Ej: EST-2024-001')
                                    ->unique(ignoreRecord: true)
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->helperText('Código único del sistema SAGA'),

                                Select::make('estado_academico')
                                    ->label('Estado Académico')
                                    ->options([
                                        'pendiente_inscripcion' => '🕒 Pendiente de Inscripción',
                                        'activo' => '✅ Activo',
                                        'inactivo' => '⏸️ Inactivo',
                                        'graduado' => '🎓 Graduado',
                                        'retirado' => '🚪 Retirado',
                                        'suspendido' => '⛔ Suspendido',
                                        'transferido' => '🔄 Transferido',
                                        'egresado' => '📜 Egresado',
                                    ])
                                    ->default('activo')
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-flag')
                                    ->helperText('Estado actual del estudiante'),
                            ]),
                    ]),

                Section::make('Información Adicional')
                    ->description('Datos complementarios del estudiante')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('tiene_discapacidad')
                                    ->label('¿Tiene alguna discapacidad?')
                                    ->helperText('Marca si el estudiante requiere atención especial')
                                    ->inline(false)
                                    ->default(false)
                                    ->required()
                                    ->live()
                                    ->columnSpan(1),

                                FileUpload::make('foto_url')
                                    ->label('Fotografía del Estudiante')
                                    ->disk('public')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                        '4:3',
                                    ])
                                    ->maxSize(2048)
                                    ->directory('estudiantes/fotos')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->helperText('Formatos: JPG, PNG, WEBP. Máximo 2MB')
                                    ->imagePreviewHeight('200')
                                    ->columnSpan(1)
                                    ->avatar()
                                    ->circleCropper(),
                            ]),

                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->placeholder('Información adicional sobre el estudiante, necesidades especiales, alergias, medicamentos, etc.')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Máximo 1000 caracteres')
                            ->columnSpanFull()
                            ->visible(fn ($get) => $get('tiene_discapacidad')),
                    ]),
            ]);
    }
}
