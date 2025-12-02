<?php

namespace App\Filament\Resources\Profesors\Schemas;

use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group as ComponentsGroup;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class ProfesorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // Sección de Foto
                ComponentsSection::make()
                    ->columnSpan(1)
                    ->schema([
                        ImageEntry::make('foto_url')
                            ->label('')
                            ->circular()
                            ->size(200)
                            ->disk('public')
                            ->defaultImageUrl(url('/images/default-avatar.png'))
                            ->alignCenter(),

                        TextEntry::make('codigo_saga')
                            ->label('Código SAGA')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-identification')
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->copyMessage('Código copiado')
                            ->alignCenter(),
                    ]),

                // Información Principal
                ComponentsGroup::make()
                    ->columnSpan(2)
                    ->schema([
                        ComponentsSection::make('Información Personal')
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('persona.nombre')
                                    ->label('Nombre')
                                    ->icon('heroicon-o-user')
                                    ->weight(FontWeight::SemiBold)
                                    ->visible(fn ($record) => $record->persona),

                                TextEntry::make('persona.apellido_pat')
                                    ->label('Apellido Paterno')
                                    ->weight(FontWeight::SemiBold)
                                    ->visible(fn ($record) => $record->persona),

                                TextEntry::make('persona.apellido_mat')
                                    ->label('Apellido Materno')
                                    ->weight(FontWeight::SemiBold)
                                    ->visible(fn ($record) => $record->persona),

                                TextEntry::make('persona.fecha_nacimiento')
                                    ->label('Fecha de Nacimiento')
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-cake')
                                    ->visible(fn ($record) => $record->persona),

                                TextEntry::make('persona.edad')
                                    ->label('Edad')
                                    ->suffix(' años')
                                    ->icon('heroicon-o-calendar')
                                    ->visible(fn ($record) => $record->persona),

                                IconEntry::make('habilitado')
                                    ->label('Estado')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->visible(fn ($record) => $record->persona),
                                TextEntry::make('sin_persona')
                                    ->label(' ')
                                    ->default('No hay información personal asociada')
                                    ->color('gray')
                                    ->weight(FontWeight::SemiBold)
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->columnSpanFull() // Ocupa todo el ancho
                                    ->alignCenter()
                                    ->visible(fn ($record) => !$record->persona),
                            ]),

                        ComponentsSection::make('Información de Contacto')
                            ->icon('heroicon-o-phone')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('persona.email_personal')
                                    ->label('Correo Electrónico')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->copyMessage('Email copiado')
                                    ->placeholder('Sin email')
                                    ->visible(fn ($record) => $record->persona),
                                    
                                TextEntry::make('persona.telefono_principal')
                                    ->label('Teléfono Principal')
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage('Teléfono copiado')
                                    ->placeholder('Sin teléfono')
                                    ->visible(fn ($record) => $record->persona),

                                TextEntry::make('persona.telefono_secundario')
                                    ->label('Teléfono Secundario')
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->copyable()
                                    ->placeholder('Sin teléfono')
                                    ->visible(fn ($record) => $record->persona),

                                TextEntry::make('persona.direccion')
                                    ->label('Dirección')
                                    ->icon('heroicon-o-map-pin')
                                    ->placeholder('Sin dirección')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => $record->persona),
                                TextEntry::make('sin_persona')
                                    ->label(' ')
                                    ->default('No hay información personal asociada')
                                    ->color('gray')
                                    ->weight(FontWeight::SemiBold)
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->columnSpanFull() // Ocupa todo el ancho
                                    ->alignCenter()
                                    ->visible(fn ($record) => !$record->persona),
                            ]),

                        ComponentsSection::make('Información Profesional')
                            ->icon('heroicon-o-academic-cap')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('profesion')
                                    ->label('Profesión')
                                    ->icon('heroicon-o-briefcase')
                                    ->badge()
                                    ->color('success')
                                    ->placeholder('Sin especificar'),

                                TextEntry::make('nacionalidad')
                                    ->label('Nacionalidad')
                                    ->icon('heroicon-o-flag')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('Sin especificar'),

                                TextEntry::make('anios_experiencia')
                                    ->label('Años de Experiencia')
                                    ->suffix(' años')
                                    ->icon('heroicon-o-clock')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('Sin especificar'),

                                TextEntry::make('persona.usuario.email')
                                    ->label('Usuario del Sistema')
                                    ->icon('heroicon-o-user-circle')
                                    ->badge()
                                    ->color('primary')
                                    ->placeholder('Sin usuario asignado'),
                            ]),
                    ]),

                // Documentos del Profesor
                ComponentsSection::make('Documentos Adjuntos')
                    ->icon('heroicon-o-document-text')
                    ->description('Documentos registrados del profesor')
                    ->columnSpanFull()
                    ->collapsed()
                    ->schema([
                        RepeatableEntry::make('documentos')
                            ->label('')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('tipoDocumento.nombre')
                                    ->label('Tipo de Documento')
                                    ->icon('heroicon-o-document')
                                    ->badge()
                                    ->color('primary')
                                    ->weight(FontWeight::Bold),

                                PdfViewerEntry::make('nombre')
                                    ->label('View the PDF')
                                    ->minHeight('40svh')
                                    ->columnSpan(2)
                                    ->fileUrl(fn ($record) => Storage::url($record->nombre_archivo)), // Set the file url if you are getting a pdf without database
                                    

                                TextEntry::make('observaciones')
                                    ->label('Observaciones')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->placeholder('Sin observaciones')
                                    ->columnSpanFull()
                                    ->markdown(),
                            ])
                            ->contained(false),
                    ]),

                // Información del Sistema
                ComponentsSection::make('Información del Sistema')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de Registro')
                            ->dateTime('d/m/Y H:i:s')
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->since()
                            ->icon('heroicon-o-arrow-path'),

                        TextEntry::make('deleted_at')
                            ->label('Fecha de Eliminación')
                            ->dateTime('d/m/Y H:i:s')
                            ->icon('heroicon-o-trash')
                            ->placeholder('No eliminado')
                            ->visible(fn ($record) => $record->deleted_at !== null),
                    ]),
            ]);
    }
}