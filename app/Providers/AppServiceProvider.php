<?php

namespace App\Providers;

use App\Models\Curso;
use App\Observers\CursoObserver;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Curso::observe(CursoObserver::class);

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->modalHeading('Paneles disponibles')
                        ->icons([
                            'finanzas' => 'heroicon-o-currency-dollar',
                            'informatica' => 'heroicon-o-home',
                            'inscripcion' => 'heroicon-o-academic-cap',
                            'profesor' => 'heroicon-o-user-group',
                        ], $asImage = false)
                        ->iconSize(24)
                        ->modalWidth('md')
                        ->labels([
                            'finanzas' => 'Finanzas',
                            'informatica' => 'Administración',
                            'inscripcion' => 'Inscripciones',
                            'profesor' => 'Profesores',
                        ]);
        });
    }
}
