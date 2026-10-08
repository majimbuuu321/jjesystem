<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InventoryHeader;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryHeaderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InventoryHeader');
    }

    public function view(AuthUser $authUser, InventoryHeader $inventoryHeader): bool
    {
        return $authUser->can('View:InventoryHeader');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InventoryHeader');
    }

    public function update(AuthUser $authUser, InventoryHeader $inventoryHeader): bool
    {
        return $authUser->can('Update:InventoryHeader');
    }

    public function delete(AuthUser $authUser, InventoryHeader $inventoryHeader): bool
    {
        return $authUser->can('Delete:InventoryHeader');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InventoryHeader');
    }

    public function restore(AuthUser $authUser, InventoryHeader $inventoryHeader): bool
    {
        return $authUser->can('Restore:InventoryHeader');
    }

    public function forceDelete(AuthUser $authUser, InventoryHeader $inventoryHeader): bool
    {
        return $authUser->can('ForceDelete:InventoryHeader');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InventoryHeader');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InventoryHeader');
    }

    public function replicate(AuthUser $authUser, InventoryHeader $inventoryHeader): bool
    {
        return $authUser->can('Replicate:InventoryHeader');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InventoryHeader');
    }

}