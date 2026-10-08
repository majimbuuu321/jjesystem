<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CreditMemoHeader;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditMemoHeaderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CreditMemoHeader');
    }

    public function view(AuthUser $authUser, CreditMemoHeader $creditMemoHeader): bool
    {
        return $authUser->can('View:CreditMemoHeader');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CreditMemoHeader');
    }

    public function update(AuthUser $authUser, CreditMemoHeader $creditMemoHeader): bool
    {
        return $authUser->can('Update:CreditMemoHeader');
    }

    public function delete(AuthUser $authUser, CreditMemoHeader $creditMemoHeader): bool
    {
        return $authUser->can('Delete:CreditMemoHeader');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CreditMemoHeader');
    }

    public function restore(AuthUser $authUser, CreditMemoHeader $creditMemoHeader): bool
    {
        return $authUser->can('Restore:CreditMemoHeader');
    }

    public function forceDelete(AuthUser $authUser, CreditMemoHeader $creditMemoHeader): bool
    {
        return $authUser->can('ForceDelete:CreditMemoHeader');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CreditMemoHeader');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CreditMemoHeader');
    }

    public function replicate(AuthUser $authUser, CreditMemoHeader $creditMemoHeader): bool
    {
        return $authUser->can('Replicate:CreditMemoHeader');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CreditMemoHeader');
    }

}