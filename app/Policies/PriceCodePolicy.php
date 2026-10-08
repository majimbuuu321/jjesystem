<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PriceCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class PriceCodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PriceCode');
    }

    public function view(AuthUser $authUser, PriceCode $priceCode): bool
    {
        return $authUser->can('View:PriceCode');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PriceCode');
    }

    public function update(AuthUser $authUser, PriceCode $priceCode): bool
    {
        return $authUser->can('Update:PriceCode');
    }

    public function delete(AuthUser $authUser, PriceCode $priceCode): bool
    {
        return $authUser->can('Delete:PriceCode');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PriceCode');
    }

    public function restore(AuthUser $authUser, PriceCode $priceCode): bool
    {
        return $authUser->can('Restore:PriceCode');
    }

    public function forceDelete(AuthUser $authUser, PriceCode $priceCode): bool
    {
        return $authUser->can('ForceDelete:PriceCode');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PriceCode');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PriceCode');
    }

    public function replicate(AuthUser $authUser, PriceCode $priceCode): bool
    {
        return $authUser->can('Replicate:PriceCode');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PriceCode');
    }

}