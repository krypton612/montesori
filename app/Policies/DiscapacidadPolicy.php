<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Discapacidad;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscapacidadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Discapacidad');
    }

    public function view(AuthUser $authUser, Discapacidad $discapacidad): bool
    {
        return $authUser->can('View:Discapacidad');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Discapacidad');
    }

    public function update(AuthUser $authUser, Discapacidad $discapacidad): bool
    {
        return $authUser->can('Update:Discapacidad');
    }

    public function delete(AuthUser $authUser, Discapacidad $discapacidad): bool
    {
        return $authUser->can('Delete:Discapacidad');
    }

    public function restore(AuthUser $authUser, Discapacidad $discapacidad): bool
    {
        return $authUser->can('Restore:Discapacidad');
    }

    public function forceDelete(AuthUser $authUser, Discapacidad $discapacidad): bool
    {
        return $authUser->can('ForceDelete:Discapacidad');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Discapacidad');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Discapacidad');
    }

    public function replicate(AuthUser $authUser, Discapacidad $discapacidad): bool
    {
        return $authUser->can('Replicate:Discapacidad');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Discapacidad');
    }

}