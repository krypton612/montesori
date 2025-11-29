<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Persona;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Persona');
    }

    public function view(AuthUser $authUser, Persona $persona): bool
    {
        return $authUser->can('View:Persona');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Persona');
    }

    public function update(AuthUser $authUser, Persona $persona): bool
    {
        return $authUser->can('Update:Persona');
    }

    public function delete(AuthUser $authUser, Persona $persona): bool
    {
        return $authUser->can('Delete:Persona');
    }

    public function restore(AuthUser $authUser, Persona $persona): bool
    {
        return $authUser->can('Restore:Persona');
    }

    public function forceDelete(AuthUser $authUser, Persona $persona): bool
    {
        return $authUser->can('ForceDelete:Persona');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Persona');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Persona');
    }

    public function replicate(AuthUser $authUser, Persona $persona): bool
    {
        return $authUser->can('Replicate:Persona');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Persona');
    }

}