<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Curso;
use Illuminate\Auth\Access\HandlesAuthorization;

class CursoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Curso');
    }

    public function view(AuthUser $authUser, Curso $curso): bool
    {
        return $authUser->can('View:Curso');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Curso');
    }

    public function update(AuthUser $authUser, Curso $curso): bool
    {
        return $authUser->can('Update:Curso');
    }

    public function delete(AuthUser $authUser, Curso $curso): bool
    {
        return $authUser->can('Delete:Curso');
    }

    public function restore(AuthUser $authUser, Curso $curso): bool
    {
        return $authUser->can('Restore:Curso');
    }

    public function forceDelete(AuthUser $authUser, Curso $curso): bool
    {
        return $authUser->can('ForceDelete:Curso');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Curso');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Curso');
    }

    public function replicate(AuthUser $authUser, Curso $curso): bool
    {
        return $authUser->can('Replicate:Curso');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Curso');
    }

}