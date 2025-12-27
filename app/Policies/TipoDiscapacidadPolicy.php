<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoDiscapacidad;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoDiscapacidadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoDiscapacidad');
    }

    public function view(AuthUser $authUser, TipoDiscapacidad $tipoDiscapacidad): bool
    {
        return $authUser->can('View:TipoDiscapacidad');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoDiscapacidad');
    }

    public function update(AuthUser $authUser, TipoDiscapacidad $tipoDiscapacidad): bool
    {
        return $authUser->can('Update:TipoDiscapacidad');
    }

    public function delete(AuthUser $authUser, TipoDiscapacidad $tipoDiscapacidad): bool
    {
        return $authUser->can('Delete:TipoDiscapacidad');
    }

    public function restore(AuthUser $authUser, TipoDiscapacidad $tipoDiscapacidad): bool
    {
        return $authUser->can('Restore:TipoDiscapacidad');
    }

    public function forceDelete(AuthUser $authUser, TipoDiscapacidad $tipoDiscapacidad): bool
    {
        return $authUser->can('ForceDelete:TipoDiscapacidad');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoDiscapacidad');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoDiscapacidad');
    }

    public function replicate(AuthUser $authUser, TipoDiscapacidad $tipoDiscapacidad): bool
    {
        return $authUser->can('Replicate:TipoDiscapacidad');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoDiscapacidad');
    }

}