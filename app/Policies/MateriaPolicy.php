<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Materia;
use Illuminate\Auth\Access\HandlesAuthorization;

class MateriaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Materia');
    }

    public function view(AuthUser $authUser, Materia $materia): bool
    {
        return $authUser->can('View:Materia');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Materia');
    }

    public function update(AuthUser $authUser, Materia $materia): bool
    {
        return $authUser->can('Update:Materia');
    }

    public function delete(AuthUser $authUser, Materia $materia): bool
    {
        return $authUser->can('Delete:Materia');
    }

    public function restore(AuthUser $authUser, Materia $materia): bool
    {
        return $authUser->can('Restore:Materia');
    }

    public function forceDelete(AuthUser $authUser, Materia $materia): bool
    {
        return $authUser->can('ForceDelete:Materia');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Materia');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Materia');
    }

    public function replicate(AuthUser $authUser, Materia $materia): bool
    {
        return $authUser->can('Replicate:Materia');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Materia');
    }

}