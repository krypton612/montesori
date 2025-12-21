<?php

namespace App\Filament\Resources\Profesors\Schemas;

use App\Models\Persona;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;

class ProfesorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // Columna izquierda: Fotografía
                
                // Columna derecha: Formulario
                ComponentsGrid::make(1)
                    ->columnSpan(2)
                    ->schema([
                        ComponentsSection::make('Información Personal')
            ->description('Datos de la persona asociada')
            ->icon('heroicon-o-user')
            ->columns(2)
            ->schema([
                // --- 1. Campo de Selección de Persona ---
                Select::make('persona_id')
                    ->label('Persona')
                    ->relationship('persona', 'nombre')
                    ->searchable(['nombre', 'apellido_pat', 'apellido_mat'])
                    ->preload()
                    ->live()
                    ->options(
                        \App\Models\Persona::whereDoesntHave('profesor')
                            ->get()
                            ->mapWithKeys(fn ($record) => [
                                $record->id => trim($record->nombre . ' ' . $record->apellido_pat . ' ' . $record->apellido_mat)
                            ])
                    )
                    ->getOptionLabelFromRecordUsing(fn (Persona $record ) => 
                        trim($record->nombre . ' ' . $record->apellido_pat . ' ' . $record->apellido_mat)
                    )
                    ->columnSpan(2)
                    ->helperText('Seleccione la persona que será profesor')
                    // ** CLAVE: Cargar y establecer el estado de los campos de visualización **
                    ->afterStateUpdated(function ($state, Set $set) {
                        // Buscar el registro de la persona
                        $persona = Persona::find($state);

                        // Limpiar los campos si no hay selección
                        if (!$persona) {
                            $set('nombre_display', null);
                            $set('apellido_pat_display', null);
                            $set('apellido_mat_display', null);
                            $set('email_display', null);
                            $set('telefono_display', null);
                            $set('edad_display', null);
                            return;
                        }

                        // Establecer el estado de los campos de solo lectura
                        $set('nombre_display', $persona->nombre);
                        $set('apellido_pat_display', $persona->apellido_pat);
                        $set('apellido_mat_display', $persona->apellido_mat);
                        $set('email_display', $persona->email_personal);
                        $set('telefono_display', $persona->telefono_principal);
                        // Asegúrate de que 'edad' esté calculado o disponible en el modelo Persona
                        $set('edad_display', $persona->edad); 
                    }),

                    // --- 2. Campos de Visualización de Datos (NO guardan en la base de datos) ---

                    // Usamos nombres de campo genéricos (e.g., 'nombre_display') sin notación de puntos.
                    // Estos campos NO deben tener el método `dehydrated(false)` si estás usando `afterStateUpdated`,
                    // pero sí deben tener `disabled()` para que no sean editables.

                    TextInput::make('nombre_display')
                        ->label('Nombre')
                        ->disabled()
                        ->placeholder('Se carga automáticamente')
                        ->prefixIcon('heroicon-o-user')
                        ->columnSpan(1)
                        ->hidden(fn (UtilitiesGet $get) => !$get('persona_id')),

                    TextInput::make('apellido_pat_display')
                        ->label('Apellido Paterno')
                        ->disabled()
                        ->placeholder('Se carga automáticamente')
                        ->columnSpan(1)
                        ->hidden(fn (UtilitiesGet $get) => !$get('persona_id')),

                    TextInput::make('apellido_mat_display')
                        ->label('Apellido Materno')
                        ->disabled()
                        ->placeholder('Se carga automáticamente')
                        ->columnSpan(1)
                        ->hidden(fn (UtilitiesGet $get) => !$get('persona_id')),

                    TextInput::make('email_display')
                        ->label('Correo Electrónico')
                        ->disabled()
                        ->placeholder('Se carga automáticamente')
                        ->prefixIcon('heroicon-o-envelope')
                        ->columnSpan(1)
                        ->hidden(fn (UtilitiesGet $get) => !$get('persona_id')),

                    TextInput::make('telefono_display')
                        ->label('Teléfono')
                        ->disabled()
                        ->placeholder('Se carga automáticamente')
                        ->prefixIcon('heroicon-o-phone')
                        ->columnSpan(1)
                        ->hidden(fn (UtilitiesGet $get) => !$get('persona_id')),

                    TextInput::make('edad_display')
                        ->label('Edad')
                        ->disabled()
                        ->placeholder('Se carga automáticamente')
                        ->suffix('años')
                        ->columnSpan(1)
                        ->hidden(fn (UtilitiesGet $get) => !$get('persona_id')),
                ]),

                        // Información del Profesor
                        ComponentsSection::make('Información del Profesor')
                            ->description('Datos específicos del profesor')
                            ->icon('heroicon-o-academic-cap')
                            ->columns(2)
                            ->schema([
                                TextInput::make('codigo_saga')
                                    ->label('Código SAGA')
                                    ->prefix('SGA-')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('Ej: PROF-2024-001')
                                    ->prefixIcon('heroicon-o-identification')
                                    ->columnSpan(1),

                                Select::make('nacionalidad')
                                    ->label('Nacionalidad')
                                    ->options([
                                        'Boliviana' => 'Boliviana',
                                        'Argentina' => 'Argentina',
                                        'Brasileña' => 'Brasileña',
                                        'Chilena' => 'Chilena',
                                        'Colombiana' => 'Colombiana',
                                        'Peruana' => 'Peruana',
                                        'Venezolana' => 'Venezolana',
                                        'Otra' => 'Otra',
                                    ])
                                    ->searchable()
                                    ->placeholder('Seleccione la nacionalidad')
                                    ->prefixIcon('heroicon-o-flag')
                                    ->columnSpan(1),

                                TextInput::make('profesion')
                                    ->label('Profesión')
                                    ->maxLength(255)
                                    ->placeholder('Ej: Ingeniero de Sistemas')
                                    ->prefixIcon('heroicon-o-briefcase')
                                    ->columnSpan(1),

                                TextInput::make('anios_experiencia')
                                    ->label('Años de Experiencia')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->suffix('años')
                                    ->placeholder('Ej: 5')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->columnSpan(1),

                                Toggle::make('habilitado')
                                    ->label('Estado')
                                    ->inline(false)
                                    ->onIcon('heroicon-s-check-circle')
                                    ->offIcon('heroicon-s-x-circle')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->default(true)
                                    ->helperText('Activo/Inactivo en el sistema')
                                    ->columnSpan(2),
                            ]),
                    ]),
                    ComponentsSection::make('Fotografía')
                    ->description('Imagen del profesor')
                    ->icon('heroicon-o-camera')
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('foto_url')
                            ->label('')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('profesores/fotos')
                            ->visibility('public')
                            ->disk('public')
                            ->maxSize(2048)
                            ->helperText('Tamaño máximo: 2MB')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400')
                            ->columnSpanFull(),
                    ]),
                    ComponentsSection::make('Documentos del Profesor')
                            ->description('Adjunte los documentos requeridos')
                            ->icon('heroicon-o-document-text')
                            ->collapsible()
                            ->columnSpan(3)
                            ->schema([
                                Repeater::make('documentos')
                                    ->relationship('documentos')
                                    ->label('Documentos Adjuntos')
                                    ->schema([
                                        ComponentsGrid::make(3)
                                            ->schema([
                                                Select::make('tipo_documento_id')
                                                    ->label('Tipo de Documento')
                                                    ->relationship(
                                                        'tipoDocumento',
                                                        'nombre',
                                                        fn ($query) => $query->where('tipo', 'documento_profesor')
                                                    )
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->placeholder('Seleccione el tipo')
                                                    ->prefixIcon('heroicon-o-document')
                                                    ->columnSpan(1)
                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                                FileUpload::make('nombre_archivo')
                                                    ->label('Archivo')
                                                    ->required()
                                                    ->directory('profesores/documentos')
                                                    ->visibility('public')
                                                    ->disk('public')
                                                    ->maxSize(5120)
                                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                    ->downloadable()
                                                    ->openable()
                                                    ->previewable()
                                                    ->helperText('PDF o imagen, máx. 5MB')
                                                    ->columnSpan(1),

                                                Textarea::make('observaciones')
                                                    ->label('Observaciones')
                                                    ->maxLength(500)
                                                    ->placeholder('Notas adicionales (opcional)')
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->addActionLabel('Agregar Documento')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => 
                                        ($state['tipo_documento_id'] ?? null)
                                            ? \App\Models\TipoDocumento::find($state['tipo_documento_id'])?->nombre 
                                            : 'Nuevo Documento'
                                    )
                                    ->defaultItems(0)
                                    ->grid(1)
                                    ->columnSpanFull(),
                            ]),

            ]);
    }
}