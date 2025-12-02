<?php

namespace App\Filament\Resources\Personas\Schemas;

use App\Models\User;
use App\Models\Usuario;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\SelectAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Components\Utilities\Set as UtilitiesSet;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Hash;
use League\Uri\Components\Component;
use Ramsey\Collection\Set as CollectionSet;

class PersonaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Información Personal')
                    ->description('Datos básicos de la persona')
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ingrese el nombre')
                            ->autocomplete(false)
                            ->columnSpan(1),

                        TextInput::make('apellido_pat')
                            ->label('Apellido Paterno')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ingrese el apellido paterno')
                            ->autocomplete(false)
                            ->columnSpan(1),

                        TextInput::make('apellido_mat')
                            ->label('Apellido Materno')
                            ->maxLength(255)
                            ->placeholder('Ingrese el apellido materno')
                            ->autocomplete(false)
                            ->columnSpan(1),

                        DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                   
                                    $edad = now()->diffInYears($state);
                                    $edad = number_format($edad, 0, '', '');
                                    $set('edad', $edad * -1);
                                }
                            })
                            ->columnSpan(1),

                        TextInput::make('edad')
                            ->numeric()
                            ->readOnly()
                            ->placeholder('Se calcula automáticamente')
                            ->suffix('años')
                            ->columnSpan(1),

                        Toggle::make('habilitado')
                            ->label('Estado')
                            ->inline(false)
                            ->onIcon('heroicon-s-check-circle')
                            ->offIcon('heroicon-s-x-circle')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->columnSpan(1),
                    ]),

                ComponentsSection::make('Información de Contacto')
                    ->description('Datos de contacto y ubicación')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('telefono_principal')
                            ->label('Teléfono Principal')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+591 77777777')
                            ->prefixIcon('heroicon-o-phone')
                            ->columnSpan(1),

                        TextInput::make('telefono_secundario')
                            ->label('Teléfono Secundario')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+591 67777777')
                            ->prefixIcon('heroicon-o-phone')
                            ->columnSpan(1),

                        TextInput::make('email_personal')
                            ->label('Correo Electrónico Personal')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('correo@ejemplo.com')
                            ->prefixIcon('heroicon-o-envelope')
                            ->columnSpan(2),

                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(500)
                            ->placeholder('Calle, número, colonia, ciudad')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->columnSpan(2),
                    ]),

                ComponentsSection::make('Acceso al Sistema')
                    ->description('Asignar o crear usuario para acceso al sistema')
                    ->icon('heroicon-o-key')
                    ->columns(2)
                    ->schema([
                        Select::make('usuario_id')
                            ->label('Usuario Existente')
                            ->relationship('usuario', 'email')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione un usuario existente')
                            ->createOptionForm([
                                ComponentsGrid::make(1)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre Completo')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ingrese el alias completo para su cuenta de usuario.')
                                            ->dehydrated()
                                            ->helperText('El nombre se toma de los datos de la persona, propietaria'),

                                        TextInput::make('email')
                                            ->label('Correo Electrónico')
                                            ->email()
                                            ->required()
                                            ->unique(Usuario::class, 'email')
                                            ->maxLength(255)
                                            ->placeholder('usuario@ejemplo.com')
                                            ->prefixIcon('heroicon-o-envelope'),

                                        TextInput::make('password')
                                            ->label('Contraseña')
                                            ->password()
                                            ->required()
                                            ->minLength(8)
                                            ->maxLength(255)
                                            ->placeholder('Mínimo 8 caracteres')
                                            ->revealable()
                                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                            ->prefixIcon('heroicon-o-lock-closed')
                                            ->helperText('La contraseña debe tener al menos 8 caracteres'),
                                    ]),
                            ])
                            ->createOptionModalHeading('Crear Nuevo Usuario')
                            
                            ->suffixAction(
                                ActionsAction::make('clearUser')
                                    ->icon('heroicon-o-x-mark')
                                    ->color('danger')
                                    ->action(fn (UtilitiesSet $set) => $set('usuario_id', null))
                                    ->hidden(fn (UtilitiesGet $get) => !$get('usuario_id'))
                            )
                            ->helperText('Opcional: Asigne un usuario existente o cree uno nuevo para dar acceso al sistema')
                            ->columnSpan(2),
                    ]),
            ]);
    }
}