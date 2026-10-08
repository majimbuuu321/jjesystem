<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RouteGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class RouteGroupPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RouteGroup');
    }

    public function view(AuthUser $authUser, RouteGroup $routeGroup): bool
    {
        return $authUser->can('View:RouteGroup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RouteGroup');
    }

    public function update(AuthUser $authUser, RouteGroup $routeGroup): bool
    {
        return $authUser->can('Update:RouteGroup');
    }

    public function delete(AuthUser $authUser, RouteGroup $routeGroup): bool
    {
        return $authUser->can('Delete:RouteGroup');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RouteGroup');
    }

    public function restore(AuthUser $authUser, RouteGroup $routeGroup): bool
    {
        return $authUser->can('Restore:RouteGroup');
    }

    public function forceDelete(AuthUser $authUser, RouteGroup $routeGroup): bool
    {
        return $authUser->can('ForceDelete:RouteGroup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RouteGroup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RouteGroup');
    }

    public function replicate(AuthUser $authUser, RouteGroup $routeGroup): bool
    {
        return $authUser->can('Replicate:RouteGroup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RouteGroup');
    }

}