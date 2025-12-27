<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoEvaluacion;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoEvaluacionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoEvaluacion');
    }

    public function view(AuthUser $authUser, TipoEvaluacion $tipoEvaluacion): bool
    {
        return $authUser->can('View:TipoEvaluacion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoEvaluacion');
    }

    public function update(AuthUser $authUser, TipoEvaluacion $tipoEvaluacion): bool
    {
        return $authUser->can('Update:TipoEvaluacion');
    }

    public function delete(AuthUser $authUser, TipoEvaluacion $tipoEvaluacion): bool
    {
        return $authUser->can('Delete:TipoEvaluacion');
    }

    public function restore(AuthUser $authUser, TipoEvaluacion $tipoEvaluacion): bool
    {
        return $authUser->can('Restore:TipoEvaluacion');
    }

    public function forceDelete(AuthUser $authUser, TipoEvaluacion $tipoEvaluacion): bool
    {
        return $authUser->can('ForceDelete:TipoEvaluacion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoEvaluacion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoEvaluacion');
    }

    public function replicate(AuthUser $authUser, TipoEvaluacion $tipoEvaluacion): bool
    {
        return $authUser->can('Replicate:TipoEvaluacion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoEvaluacion');
    }

}