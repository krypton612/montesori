<?php

namespace App\Filament\Resources\Estudiantes\Schemas;

use App\Models\Estudiante;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Schema;

class EstudianteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Estudiante')
                    ->description('Datos personales y de identificación')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('foto_url')
                                    ->disk('public')
                                    ->label('Fotografía')
                                    ->circular()
                                    ->defaultImageUrl(url('/images/default-avatar.png'))
                                    ->height(120)
                                    ->extraAttributes(['class' => 'shadow-lg'])
                                    ->columnSpan(1),

                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('persona.nombre_completo')
                                            ->label('Nombre Completo')
                                            ->icon('heroicon-o-user-circle')
                                            ->iconColor('primary')
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->placeholder('Sin nombre'),

                                        TextEntry::make('persona.ci')
                                            ->label('Carnet de Identidad')
                                            ->icon('heroicon-o-identification')
                                            ->iconColor('gray')
                                            ->badge()
                                            ->color('gray')
                                            ->placeholder('Sin CI')
                                            ->copyable()
                                            ->copyMessage('CI copiado'),

                                        TextEntry::make('codigo_saga')
                                            ->label('Código SAGA')
                                            ->icon('heroicon-o-hashtag')
                                            ->iconColor('info')
                                            ->badge()
                                            ->color('info')
                                            ->placeholder('Sin código')
                                            ->copyable()
                                            ->copyMessage('Código copiado'),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Datos de Contacto')
                    ->description('Información de contacto de la persona')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('persona.email')
                                    ->label('Correo Electrónico')
                                    ->icon('heroicon-o-envelope')
                                    ->iconColor('blue')
                                    ->placeholder('Sin correo')
                                    ->copyable()
                                    ->copyMessage('Email copiado'),

                                TextEntry::make('persona.telefono_principal')
                                    ->label('Teléfono Principal')
                                    ->icon('heroicon-o-phone')
                                    ->iconColor('green')
                                    ->placeholder('Sin teléfono')
                                    ->copyable()
                                    ->copyMessage('Teléfono copiado'),

                                TextEntry::make('persona.telefono_secundario')
                                    ->label('Teléfono Secundario')
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->iconColor('gray')
                                    ->placeholder('Sin teléfono')
                                    ->copyable(),
                            ]),

                        TextEntry::make('persona.direccion')
                            ->label('Dirección')
                            ->icon('heroicon-o-map-pin')
                            ->iconColor('red')
                            ->placeholder('Sin dirección registrada')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Información Académica')
                    ->description('Estado y datos académicos del estudiante')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('estado_academico')
                                    ->label('Estado Académico')
                                    ->badge()
                                    ->color(fn (string $state): string => match($state) {
                                        'activo' => 'success',
                                        'inactivo' => 'gray',
                                        'graduado' => 'info',
                                        'retirado' => 'warning',
                                        'suspendido' => 'danger',
                                        'transferido' => 'purple',
                                        'egresado' => 'cyan',
                                        default => 'gray',
                                    })
                                    ->icon(fn (string $state): string => match($state) {
                                        'activo' => 'heroicon-o-check-circle',
                                        'inactivo' => 'heroicon-o-pause-circle',
                                        'graduado' => 'heroicon-o-academic-cap',
                                        'retirado' => 'heroicon-o-arrow-right-on-rectangle',
                                        'suspendido' => 'heroicon-o-no-symbol',
                                        'transferido' => 'heroicon-o-arrow-path',
                                        'egresado' => 'heroicon-o-document-check',
                                        default => 'heroicon-o-question-mark-circle',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match($state) {
                                        'activo' => '✅ Activo',
                                        'inactivo' => '⏸️ Inactivo',
                                        'graduado' => '🎓 Graduado',
                                        'retirado' => '🚪 Retirado',
                                        'suspendido' => '⛔ Suspendido',
                                        'transferido' => '🔄 Transferido',
                                        'egresado' => '📜 Egresado',
                                        default => ucfirst($state),
                                    })
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('Sin estado'),

                                TextEntry::make('persona.fecha_nacimiento')
                                    ->label('Fecha de Nacimiento')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-cake')
                                    ->iconColor('pink')
                                    ->placeholder('Sin fecha'),

                                TextEntry::make('persona.edad')
                                    ->label('Edad')
                                    ->numeric()
                                    ->suffix(' años')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('Sin edad'),
                            ]),

                        IconEntry::make('persona.habilitado')
                            ->label('Persona Habilitada')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Necesidades Especiales')
                    ->description('Información sobre discapacidad y observaciones')
                    ->icon('heroicon-o-heart')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                IconEntry::make('tiene_discapacidad')
                                    ->label('¿Tiene Discapacidad?')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-exclamation-triangle')
                                    ->falseIcon('heroicon-o-check-circle')
                                    ->trueColor('warning')
                                    ->falseColor('success')
                                    ->grow(false),
                                TextEntry::make('tiene_discapacidad')
                                    ->label('Estado de Discapacidad')
                                    ->formatStateUsing(fn (bool $state): string =>
                                    $state ? 'Requiere atención especial' : 'No requiere atención especial'
                                    )
                                    ->icon(fn (bool $state): string =>
                                    $state ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'
                                    )
                                    ->iconColor(fn (bool $state): string =>
                                    $state ? 'warning' : 'success'
                                    )
                                    ->weight(FontWeight::Medium),
                            ]),

                        TextEntry::make('observaciones')
                            ->label('Observaciones y Notas')
                            ->icon('heroicon-o-document-text')
                            ->iconColor('gray')
                            ->placeholder('Sin observaciones registradas')
                            ->markdown()
                            ->columnSpanFull()
                            ->visible(fn (Estudiante $record): bool => $record->tiene_discapacidad),
                    ])
                    ->collapsible()
                    ->collapsed(fn (Estudiante $record): bool => !$record->tiene_discapacidad),

                Section::make('Apoderados')
                    ->description('Tutores o responsables del estudiante')
                    ->icon('heroicon-o-users')
                    ->schema([
                        RepeatableEntry::make('apoderados')
                            ->label('')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('persona.nombre_completo')
                                            ->label('Nombre del Apoderado')
                                            ->icon('heroicon-o-user')
                                            ->iconColor('purple')
                                            ->weight(FontWeight::SemiBold)
                                            ->color('purple')
                                            ->columnSpan(2),

                                        TextEntry::make('pivot.parentestco')
                                            ->label('Parentesco')
                                            ->badge()
                                            ->color('info')
                                            ->icon('heroicon-o-user-group'),

                                        IconEntry::make('pivot.es_principal')
                                            ->label('Principal')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-star')
                                            ->falseIcon('heroicon-o-user')
                                            ->trueColor('warning')
                                            ->falseColor('gray'),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('persona.telefono_principal')
                                            ->label('Teléfono')
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('Sin teléfono')
                                            ->copyable(),

                                        TextEntry::make('persona.email')
                                            ->label('Email')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('Sin email')
                                            ->copyable(),

                                        IconEntry::make('pivot.vive_con_el')
                                            ->label('Vive con el estudiante')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-home')
                                            ->falseIcon('heroicon-o-home-modern')
                                            ->trueColor('success')
                                            ->falseColor('gray'),

                                        TextEntry::make('persona.ci')
                                            ->label('CI')
                                            ->badge()
                                            ->color('gray')
                                            ->placeholder('Sin CI')
                                            ->copyable(),
                                    ]),
                            ])
                            ->contained(true)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Estudiante $record): bool => $record->apoderados()->exists())
                    ->collapsible(),

                Section::make('Discapacidades Registradas')
                    ->description('Listado de discapacidades del estudiante')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        RepeatableEntry::make('discapacidades')
                            ->label('')
                            ->schema([
                                TextEntry::make('nombre')
                                    ->label('Tipo de Discapacidad')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('pivot.observacion')
                                    ->label('Observaciones')
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('Sin observaciones')
                                    ->markdown()
                                    ->columnSpanFull(),
                            ])
                            ->contained(true)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Estudiante $record): bool =>
                        $record->tiene_discapacidad && $record->discapacidades()->exists()
                    )
                    ->collapsible()
                    ->collapsed(),

                Section::make('Metadatos del Sistema')
                    ->schema([
                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Fecha de Registro')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success')
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label('Última Actualización')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-arrow-path')
                                    ->color('gray')
                                    ->since()
                                    ->placeholder('-'),

                                TextEntry::make('deleted_at')
                                    ->label('Fecha de Eliminación')
                                    ->dateTime('d/m/Y H:i')
                                    ->badge()
                                    ->color('danger')
                                    ->icon('heroicon-o-trash')
                                    ->visible(fn (Estudiante $record): bool => $record->trashed()),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
