<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Routes;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoutesPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Routes');
    }

    public function view(AuthUser $authUser, Routes $routes): bool
    {
        return $authUser->can('View:Routes');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Routes');
    }

    public function update(AuthUser $authUser, Routes $routes): bool
    {
        return $authUser->can('Update:Routes');
    }

    public function delete(AuthUser $authUser, Routes $routes): bool
    {
        return $authUser->can('Delete:Routes');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Routes');
    }

    public function restore(AuthUser $authUser, Routes $routes): bool
    {
        return $authUser->can('Restore:Routes');
    }

    public function forceDelete(AuthUser $authUser, Routes $routes): bool
    {
        return $authUser->can('ForceDelete:Routes');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Routes');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Routes');
    }

    public function replicate(AuthUser $authUser, Routes $routes): bool
    {
        return $authUser->can('Replicate:Routes');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Routes');
    }

}