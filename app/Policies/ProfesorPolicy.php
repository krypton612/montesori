<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Profesor;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfesorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Profesor');
    }

    public function view(AuthUser $authUser, Profesor $profesor): bool
    {
        return $authUser->can('View:Profesor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Profesor');
    }

    public function update(AuthUser $authUser, Profesor $profesor): bool
    {
        return $authUser->can('Update:Profesor');
    }

    public function delete(AuthUser $authUser, Profesor $profesor): bool
    {
        return $authUser->can('Delete:Profesor');
    }

    public function restore(AuthUser $authUser, Profesor $profesor): bool
    {
        return $authUser->can('Restore:Profesor');
    }

    public function forceDelete(AuthUser $authUser, Profesor $profesor): bool
    {
        return $authUser->can('ForceDelete:Profesor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Profesor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Profesor');
    }

    public function replicate(AuthUser $authUser, Profesor $profesor): bool
    {
        return $authUser->can('Replicate:Profesor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Profesor');
    }

}