<?php

namespace App\Filament\Resources\Materias\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class MallasRelationManager extends RelationManager
{
    protected static string $relationship = 'mallas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('anio')
                    ->searchable()
                    ->options([
                        2023 => '2023',
                        2024 => '2024',
                        2025 => '2025',
                        2026 => '2026',
                        2027 => '2027',
                    ])
                    ->required(),
                FileUpload::make('nombre_archivo')
                    ->required()
                    ->directory('mallas/documentos')
                    ->visibility('public')
                    ->disk('public')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['application/pdf'])
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->helperText('PDF o imagen, máx. 5MB')
                    ->columnSpan(1),


                Toggle::make('habilitado')
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('anio')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->numeric()
                    ->placeholder('-'),
                PdfViewerEntry::make('nombre')
                    ->label('Documento')
                    ->columnSpan(2)
                    ->fileUrl(fn ($record) => Storage::url($record->nombre_archivo)), // Set the file url if you are getting a pdf without database


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('anio')
            ->columns([
                TextColumn::make('anio')
                    ->label("Año")
                    ->sortable(),
                TextColumn::make('nombre_archivo')
                    ->label("Documento")
                    ->searchable(),
                IconColumn::make('habilitado')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
