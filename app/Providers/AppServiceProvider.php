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
                        ->modalWidth('md')
                        ->icons([
                            'informatica' => 'heroicon-o-home',
                            'inscripcion' => 'heroicon-o-academic-cap',
                        ], $asImage = false)
                        ->iconSize(32)
                        ->labels([
                            'informatica' => 'Administración',
                            'inscripcion' => 'Inscripciones',
                        ]);
        });
    }
}
