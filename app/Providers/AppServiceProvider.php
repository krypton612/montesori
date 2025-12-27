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
            $user = auth()->user();
            
            // Determinar qué paneles excluir basándose en los permisos
            $excludedPanels = [];
            
            if ($user) {
                if (!$user->hasPermissionTo('AccessFinanzasPanel')) {
                    $excludedPanels[] = 'finanzas';
                }
                
                if (!$user->hasPermissionTo('AccessAdminPanel')) {
                    $excludedPanels[] = 'informatica';
                }
                
                if (!$user->hasPermissionTo('AccessInscripcionPanel')) {
                    $excludedPanels[] = 'inscripcion';
                }
                
                if (!$user->hasPermissionTo('AccessProfesorPanel')) {
                    $excludedPanels[] = 'profesor';
                }
            }
            
            $panelSwitch
                ->modalHeading('Paneles disponibles')
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
                ])
                // Excluir los paneles a los que el usuario no tiene acceso
                ->excludes($excludedPanels)
                // Controlar quién puede ver el switch (usuarios con acceso a más de un panel)
                ->canSwitchPanels(function () use ($user, $excludedPanels): bool {
                    if (!$user) {
                        return false;
                    }
                    
                    // Calcular cuántos paneles tiene disponibles
                    $totalPanels = 4; // finanzas, informatica, inscripcion, profesor
                    $availablePanels = $totalPanels - count($excludedPanels);
                    
                    // Mostrar el switch solo si tiene acceso a más de un panel
                    return $availablePanels > 1;
                });
        });
    }
}
