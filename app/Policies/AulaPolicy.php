<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Aula;
use Illuminate\Auth\Access\HandlesAuthorization;

class AulaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Aula');
    }

    public function view(AuthUser $authUser, Aula $aula): bool
    {
        return $authUser->can('View:Aula');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Aula');
    }

    public function update(AuthUser $authUser, Aula $aula): bool
    {
        return $authUser->can('Update:Aula');
    }

    public function delete(AuthUser $authUser, Aula $aula): bool
    {
        return $authUser->can('Delete:Aula');
    }

    public function restore(AuthUser $authUser, Aula $aula): bool
    {
        return $authUser->can('Restore:Aula');
    }

    public function forceDelete(AuthUser $authUser, Aula $aula): bool
    {
        return $authUser->can('ForceDelete:Aula');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Aula');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Aula');
    }

    public function replicate(AuthUser $authUser, Aula $aula): bool
    {
        return $authUser->can('Replicate:Aula');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Aula');
    }

}