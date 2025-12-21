<?php

namespace App\Filament\Resources\Aulas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AulaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo')
                    ->prefix('AULA-')
                    ->placeholder("AB12")
                    ->unique(ignoreRecord: true)
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        // Al cargar el form, quita el prefix
                        $component->state(str_replace('AUL-', '', $state));
                    })
                    ->dehydrateStateUsing(fn ($state) => 'AUL-' . $state)
                    ->prefixIcon(Heroicon::OutlinedTag)
                    ->required(),
                TextInput::make('numero')
                    ->placeholder("101")
                    ->numeric()
                    ->prefixIcon(Heroicon::OutlinedHashtag)
                    ->required(),
                TextInput::make('capacidad')
                    ->placeholder("30")
                    ->prefixIcon(Heroicon::OutlinedUsers)
                    ->required()
                    ->numeric(),

                Toggle::make('habilitado')
                    ->label('Habilitado')
                    ->onIcon(Heroicon::OutlinedCheckCircle)
                    ->offIcon(Heroicon::OutlinedXCircle)
                    ->required(),
            ]);
    }
}
