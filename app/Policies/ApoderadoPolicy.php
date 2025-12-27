<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Apoderado;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApoderadoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Apoderado');
    }

    public function view(AuthUser $authUser, Apoderado $apoderado): bool
    {
        return $authUser->can('View:Apoderado');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Apoderado');
    }

    public function update(AuthUser $authUser, Apoderado $apoderado): bool
    {
        return $authUser->can('Update:Apoderado');
    }

    public function delete(AuthUser $authUser, Apoderado $apoderado): bool
    {
        return $authUser->can('Delete:Apoderado');
    }

    public function restore(AuthUser $authUser, Apoderado $apoderado): bool
    {
        return $authUser->can('Restore:Apoderado');
    }

    public function forceDelete(AuthUser $authUser, Apoderado $apoderado): bool
    {
        return $authUser->can('ForceDelete:Apoderado');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Apoderado');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Apoderado');
    }

    public function replicate(AuthUser $authUser, Apoderado $apoderado): bool
    {
        return $authUser->can('Replicate:Apoderado');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Apoderado');
    }

}