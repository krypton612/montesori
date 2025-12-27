<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Estudiante;
use Illuminate\Auth\Access\HandlesAuthorization;

class EstudiantePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Estudiante');
    }

    public function view(AuthUser $authUser, Estudiante $estudiante): bool
    {
        return $authUser->can('View:Estudiante');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Estudiante');
    }

    public function update(AuthUser $authUser, Estudiante $estudiante): bool
    {
        return $authUser->can('Update:Estudiante');
    }

    public function delete(AuthUser $authUser, Estudiante $estudiante): bool
    {
        return $authUser->can('Delete:Estudiante');
    }

    public function restore(AuthUser $authUser, Estudiante $estudiante): bool
    {
        return $authUser->can('Restore:Estudiante');
    }

    public function forceDelete(AuthUser $authUser, Estudiante $estudiante): bool
    {
        return $authUser->can('ForceDelete:Estudiante');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Estudiante');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Estudiante');
    }

    public function replicate(AuthUser $authUser, Estudiante $estudiante): bool
    {
        return $authUser->can('Replicate:Estudiante');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Estudiante');
    }

}