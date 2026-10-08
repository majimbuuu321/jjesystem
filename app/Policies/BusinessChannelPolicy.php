<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BusinessChannel;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessChannelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BusinessChannel');
    }

    public function view(AuthUser $authUser, BusinessChannel $businessChannel): bool
    {
        return $authUser->can('View:BusinessChannel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BusinessChannel');
    }

    public function update(AuthUser $authUser, BusinessChannel $businessChannel): bool
    {
        return $authUser->can('Update:BusinessChannel');
    }

    public function delete(AuthUser $authUser, BusinessChannel $businessChannel): bool
    {
        return $authUser->can('Delete:BusinessChannel');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:BusinessChannel');
    }

    public function restore(AuthUser $authUser, BusinessChannel $businessChannel): bool
    {
        return $authUser->can('Restore:BusinessChannel');
    }

    public function forceDelete(AuthUser $authUser, BusinessChannel $businessChannel): bool
    {
        return $authUser->can('ForceDelete:BusinessChannel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BusinessChannel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BusinessChannel');
    }

    public function replicate(AuthUser $authUser, BusinessChannel $businessChannel): bool
    {
        return $authUser->can('Replicate:BusinessChannel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BusinessChannel');
    }

}