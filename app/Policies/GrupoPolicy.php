<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Grupo;
use Illuminate\Auth\Access\HandlesAuthorization;

class GrupoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Grupo');
    }

    public function view(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('View:Grupo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Grupo');
    }

    public function update(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('Update:Grupo');
    }

    public function delete(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('Delete:Grupo');
    }

    public function restore(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('Restore:Grupo');
    }

    public function forceDelete(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('ForceDelete:Grupo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Grupo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Grupo');
    }

    public function replicate(AuthUser $authUser, Grupo $grupo): bool
    {
        return $authUser->can('Replicate:Grupo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Grupo');
    }

}