<?php

namespace App\Filament\Resources\TipoEvaluacions\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TipoEvaluacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->prefix('EVALUACION ')
                    ->placeholder('Proceso')
                    ->required(),
                RichEditor::make('descripcion')
                    ->columnSpanFull(),
                Toggle::make('es_formativa')
                    ->required(),
                Toggle::make('es_sumativa')
                    ->required(),
                Toggle::make('visible')
                    ->required(),
            ]);
    }
}
