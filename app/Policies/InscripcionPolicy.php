<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Inscripcion;
use Illuminate\Auth\Access\HandlesAuthorization;

class InscripcionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Inscripcion');
    }

    public function view(AuthUser $authUser, Inscripcion $inscripcion): bool
    {
        return $authUser->can('View:Inscripcion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Inscripcion');
    }

    public function update(AuthUser $authUser, Inscripcion $inscripcion): bool
    {
        return $authUser->can('Update:Inscripcion');
    }

    public function delete(AuthUser $authUser, Inscripcion $inscripcion): bool
    {
        return $authUser->can('Delete:Inscripcion');
    }

    public function restore(AuthUser $authUser, Inscripcion $inscripcion): bool
    {
        return $authUser->can('Restore:Inscripcion');
    }

    public function forceDelete(AuthUser $authUser, Inscripcion $inscripcion): bool
    {
        return $authUser->can('ForceDelete:Inscripcion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Inscripcion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Inscripcion');
    }

    public function replicate(AuthUser $authUser, Inscripcion $inscripcion): bool
    {
        return $authUser->can('Replicate:Inscripcion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Inscripcion');
    }

}