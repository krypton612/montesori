<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsuarioPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Usuario');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Usuario');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Usuario');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Usuario');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Usuario');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Usuario');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Usuario');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Usuario');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Usuario');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Usuario');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Usuario');
    }

}