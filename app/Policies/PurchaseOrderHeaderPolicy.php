<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PurchaseOrderHeader;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderHeaderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PurchaseOrderHeader');
    }

    public function view(AuthUser $authUser, PurchaseOrderHeader $purchaseOrderHeader): bool
    {
        return $authUser->can('View:PurchaseOrderHeader');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PurchaseOrderHeader');
    }

    public function update(AuthUser $authUser, PurchaseOrderHeader $purchaseOrderHeader): bool
    {
        return $authUser->can('Update:PurchaseOrderHeader');
    }

    public function delete(AuthUser $authUser, PurchaseOrderHeader $purchaseOrderHeader): bool
    {
        return $authUser->can('Delete:PurchaseOrderHeader');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PurchaseOrderHeader');
    }

    public function restore(AuthUser $authUser, PurchaseOrderHeader $purchaseOrderHeader): bool
    {
        return $authUser->can('Restore:PurchaseOrderHeader');
    }

    public function forceDelete(AuthUser $authUser, PurchaseOrderHeader $purchaseOrderHeader): bool
    {
        return $authUser->can('ForceDelete:PurchaseOrderHeader');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PurchaseOrderHeader');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PurchaseOrderHeader');
    }

    public function replicate(AuthUser $authUser, PurchaseOrderHeader $purchaseOrderHeader): bool
    {
        return $authUser->can('Replicate:PurchaseOrderHeader');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PurchaseOrderHeader');
    }

}