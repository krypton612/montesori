<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Estado;
use Illuminate\Auth\Access\HandlesAuthorization;

class EstadoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Estado');
    }

    public function view(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('View:Estado');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Estado');
    }

    public function update(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('Update:Estado');
    }

    public function delete(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('Delete:Estado');
    }

    public function restore(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('Restore:Estado');
    }

    public function forceDelete(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('ForceDelete:Estado');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Estado');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Estado');
    }

    public function replicate(AuthUser $authUser, Estado $estado): bool
    {
        return $authUser->can('Replicate:Estado');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Estado');
    }

}