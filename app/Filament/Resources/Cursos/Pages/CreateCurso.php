<?php

namespace App\Filament\Resources\Cursos\Pages;

use App\Filament\Resources\Cursos\CursoResource;
use App\Services\EvaluacionGeneratorService;
use Filament\Resources\Pages\CreateRecord;

class CreateCurso extends CreateRecord
{
    protected static string $resource = CursoResource::class;

    protected $tiposEvaluacion = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Capturar los tipos de evaluación desde $this->data
        $this->tiposEvaluacion = $this->data['tipos_evaluacion'] ?? [];

        return $data;
    }

    protected function afterCreate(): void
    {
        // Generar evaluaciones después de crear el curso
        $this->sincronizarEvaluaciones();
    }

    protected function sincronizarEvaluaciones(): void
    {
        $tiposEvaluacion = $this->tiposEvaluacion ?? [];

        // Solo crear evaluaciones si hay tipos seleccionados
        if (!empty($tiposEvaluacion)) {
            $generatorService = new EvaluacionGeneratorService();
            $generatorService->generarEvaluaciones($this->record, $tiposEvaluacion);
        }
    }

    protected function getRedirectUrl(): string
    {
        // Redirigir a la página de edición después de crear
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
