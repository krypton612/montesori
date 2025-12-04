<?php

namespace App\Filament\Resources\Cursos\Pages;

use App\Filament\Resources\Cursos\CursoResource;
use App\Services\EvaluacionGeneratorService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCurso extends EditRecord
{
    protected static string $resource = CursoResource::class;



    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Guardar los tipos de evaluación para sincronizar después
        $this->tiposEvaluacion = $data['tipo_evaluacion'] ?? [];

        dd($this->tiposEvaluacion);
        // Remover del array para no intentar guardarlo en la tabla cursos
        unset($data['tipo_evaluacion']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Sincronizar evaluaciones después de actualizar el curso
        $this->sincronizarEvaluaciones();
    }

    protected function sincronizarEvaluaciones(): void
    {
        $tiposEvaluacion = $this->tiposEvaluacion ?? [];

        // Obtener los tipos actuales
        $tiposActuales = $this->record->evaluaciones()
            ->pluck('tipo_evaluacion_id')
            ->unique()
            ->toArray();

        // Tipos a agregar
        $tiposAgregar = array_diff($tiposEvaluacion, $tiposActuales);

        // Tipos a eliminar
        $tiposEliminar = array_diff($tiposActuales, $tiposEvaluacion);

        // Agregar nuevas evaluaciones
        if (!empty($tiposAgregar)) {
            $generatorService = new EvaluacionGeneratorService();
            $generatorService->generarEvaluaciones($this->record, $tiposAgregar);
        }

        // Eliminar evaluaciones de tipos no seleccionados
        if (!empty($tiposEliminar)) {
            $this->record->evaluaciones()
                ->whereIn('tipo_evaluacion_id', $tiposEliminar)
                ->delete();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
