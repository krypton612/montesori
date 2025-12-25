<?php

namespace App\Filament\Inscripcion\Resources\Inscripcions\Tables;
use Faker\Core\Color;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InscripcionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    // FOTO
                    ImageColumn::make('estudiante.foto_url')
                        ->disk('public')
                        ->label('Foto')
                        ->extraAttributes([
                            'style' => 'object-fit: cover; width: 100%; height: 100%; border-2;',
                        ])
                        ->height(200)           // altura completa deseada
                        ->width(200)
                        ->alignCenter()
                    ,

                    // DATOS APILADOS
                    Stack::make([
                        TextColumn::make('codigo_inscripcion')
                            ->label('Código de Inscripción')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('estudiante.persona.nombre_completo')
                            ->label('Estudiante')
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('grupo.nombre')
                            ->label('Grupo')
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('curso.nombre_completo')
                            ->label('Curso')
                            ->searchable()
                            ->sortable()
                            ->badge('secondary')
                            ,

                        TextColumn::make('gestion.nombre')
                            ->label('Gestión')
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('fecha_inscripcion')
                            ->label('Fecha')
                            ->date()
                            ->sortable(),


                        TextColumn::make('estado.nombre')
                            ->label('Estado')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'PENDIENTE DE ENVIO' => 'warning',
                                'APROBADA'           => 'success',
                                'RECHAZADA'          => 'danger',
                                default              => 'gray',
                            })
                            ->sortable(),
                    ])
                    ->alignCenter()
                    ,
                ])->from('lg'), // Hace que el split solo se aplique en pantallas grandes
            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ActionGroup::make([
                    Action::make('Hoja de Inscripción')
                        ->icon(Heroicon::OutlinedDocumentArrowDown)
                        ->color('secondary')
                        ->modalHeading('Vista Previa - Hoja de Inscripción')
                        ->modalSubmitActionLabel('Descargar PDF')
                        ->modalCancelActionLabel('Cerrar')
                        ->modalWidth('5xl')
                        ->modalContent(fn ($record) => view('modals.preview-document', [
                            'url' => route('documentos.inscripcion_hoja_datos.preview', $record->id),
                            'downloadUrl' => route('documentos.inscripcion_hoja_datos.descargar', $record->id)
                        ]))
                        ->action(function ($record) {
                            return redirect()->route('documentos.inscripcion_hoja_datos.descargar', $record->id);
                        }),
                    Action::make('Hoja de Servicios')
                        ->icon(Heroicon::OutlinedDocumentArrowDown)
                        ->color('secondary')
                        ->modalHeading('Vista Previa - Hoja de Compromiso')
                        ->modalSubmitActionLabel('Descargar PDF')
                        ->modalCancelActionLabel('Cerrar')
                        ->modalWidth('5xl')
                        ->modalContent(fn ($record) => view('modals.preview-document', [
                            'url' => route('documentos.preview.compromiso', $record->id),
                            'downloadUrl' => route('documentos.descargar.compromiso', $record->id)
                        ]))
                        ->action(function ($record) {
                            return redirect()->route('documentos.descargar.compromiso', $record->id);
                        }),
                    Action::make('Enviar Hojas por Email')
                        ->icon('heroicon-o-envelope')
                        ->color('secondary')
                        ->openUrlInNewTab()
                ])
                    ->label('Documentos')
                    ->outlined()
                    ->icon(Heroicon::DocumentArrowDown)
                    ->button()

            ])

            ->toolbarActions([

            ]);
    }
}
