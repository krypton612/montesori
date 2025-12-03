<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Gestion;
use Illuminate\Auth\Access\HandlesAuthorization;

class GestionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Gestion');
    }

    public function view(AuthUser $authUser, Gestion $gestion): bool
    {
        return $authUser->can('View:Gestion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Gestion');
    }

    public function update(AuthUser $authUser, Gestion $gestion): bool
    {
        return $authUser->can('Update:Gestion');
    }

    public function delete(AuthUser $authUser, Gestion $gestion): bool
    {
        return $authUser->can('Delete:Gestion');
    }

    public function restore(AuthUser $authUser, Gestion $gestion): bool
    {
        return $authUser->can('Restore:Gestion');
    }

    public function forceDelete(AuthUser $authUser, Gestion $gestion): bool
    {
        return $authUser->can('ForceDelete:Gestion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Gestion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Gestion');
    }

    public function replicate(AuthUser $authUser, Gestion $gestion): bool
    {
        return $authUser->can('Replicate:Gestion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Gestion');
    }

}