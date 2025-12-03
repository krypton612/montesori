<?php

namespace App\Filament\Resources\Turnos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TurnoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->prefix("Turno ")
                    ->prefixIcon(Heroicon::OutlinedClock)
                    ->required(),
                TimePicker::make('hora_inicio')
                    ->prefixIcon(Heroicon::OutlinedClock)
                ,

                TimePicker::make('hora_fin')
                    ->prefixIcon(Heroicon::OutlinedClock)
                ,
                Toggle::make('habilitado')
                    ->offIcon(Heroicon::OutlinedCheckBadge)
                    ->onIcon(Heroicon::CheckBadge)
                    ->required(),
                Select::make('estado_id')
                    ->relationship(
                        name: 'estado',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: fn ($query) => $query->where('tipo', 'turno')
                    )
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }
}
