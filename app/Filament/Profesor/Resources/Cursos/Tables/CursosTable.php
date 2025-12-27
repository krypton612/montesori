<?php

namespace App\Filament\Profesor\Resources\Cursos\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 Split::make([
                    ImageColumn::make('profesor.foto_url')
                        ->disk('public')
                        ->label('Foto')
                        ->extraAttributes([
                            'style' => 'object-fit: cover; width: 100%; height: 100%; border-2;',
                        ])
                        ->height(100)           // altura completa deseada
                        ->width(100)
                        ->alignCenter()
                    ,
                    Stack::make([
                        TextColumn::make('nombre_completo')
                            ->label('Nombre')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->searchable()
                            ->icon(Heroicon::AcademicCap)
                            ->sortable(),
                        TextColumn::make('turno.nombre')
                            ->label('Turno')
                            ->searchable()
                            ->sortable()
                            ->badge('secondary')
                            ,
                        TextColumn::make('profesor.persona.nombre_completo')
                            ->label('Cupo Máximo')
                            ->searchable()
                            ->sortable(),
                        TextColumn::make('total_inscripciones')
                            ->label('Cupo Actual')
                            ->searchable()
                            ->prefix('Inscritos: ')
                            ->sortable(),
                        TextColumn::make('estado.nombre')
                            ->label('Estado')
                            ->searchable()
                            ->sortable()
                            ->badge('secondary')
                            ,
                     ])
                 ])->from('lg'),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                
            ]);
    }
    
}
