<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InventoryType;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InventoryType');
    }

    public function view(AuthUser $authUser, InventoryType $inventoryType): bool
    {
        return $authUser->can('View:InventoryType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InventoryType');
    }

    public function update(AuthUser $authUser, InventoryType $inventoryType): bool
    {
        return $authUser->can('Update:InventoryType');
    }

    public function delete(AuthUser $authUser, InventoryType $inventoryType): bool
    {
        return $authUser->can('Delete:InventoryType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InventoryType');
    }

    public function restore(AuthUser $authUser, InventoryType $inventoryType): bool
    {
        return $authUser->can('Restore:InventoryType');
    }

    public function forceDelete(AuthUser $authUser, InventoryType $inventoryType): bool
    {
        return $authUser->can('ForceDelete:InventoryType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InventoryType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InventoryType');
    }

    public function replicate(AuthUser $authUser, InventoryType $inventoryType): bool
    {
        return $authUser->can('Replicate:InventoryType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InventoryType');
    }

}