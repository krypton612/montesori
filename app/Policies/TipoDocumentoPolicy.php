<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TipoDocumento;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoDocumentoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TipoDocumento');
    }

    public function view(AuthUser $authUser, TipoDocumento $tipoDocumento): bool
    {
        return $authUser->can('View:TipoDocumento');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TipoDocumento');
    }

    public function update(AuthUser $authUser, TipoDocumento $tipoDocumento): bool
    {
        return $authUser->can('Update:TipoDocumento');
    }

    public function delete(AuthUser $authUser, TipoDocumento $tipoDocumento): bool
    {
        return $authUser->can('Delete:TipoDocumento');
    }

    public function restore(AuthUser $authUser, TipoDocumento $tipoDocumento): bool
    {
        return $authUser->can('Restore:TipoDocumento');
    }

    public function forceDelete(AuthUser $authUser, TipoDocumento $tipoDocumento): bool
    {
        return $authUser->can('ForceDelete:TipoDocumento');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TipoDocumento');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TipoDocumento');
    }

    public function replicate(AuthUser $authUser, TipoDocumento $tipoDocumento): bool
    {
        return $authUser->can('Replicate:TipoDocumento');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TipoDocumento');
    }

}