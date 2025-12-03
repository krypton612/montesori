<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Turno;
use Illuminate\Auth\Access\HandlesAuthorization;

class TurnoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Turno');
    }

    public function view(AuthUser $authUser, Turno $turno): bool
    {
        return $authUser->can('View:Turno');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Turno');
    }

    public function update(AuthUser $authUser, Turno $turno): bool
    {
        return $authUser->can('Update:Turno');
    }

    public function delete(AuthUser $authUser, Turno $turno): bool
    {
        return $authUser->can('Delete:Turno');
    }

    public function restore(AuthUser $authUser, Turno $turno): bool
    {
        return $authUser->can('Restore:Turno');
    }

    public function forceDelete(AuthUser $authUser, Turno $turno): bool
    {
        return $authUser->can('ForceDelete:Turno');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Turno');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Turno');
    }

    public function replicate(AuthUser $authUser, Turno $turno): bool
    {
        return $authUser->can('Replicate:Turno');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Turno');
    }

}