<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PaymentTerms;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentTermsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentTerms');
    }

    public function view(AuthUser $authUser, PaymentTerms $paymentTerms): bool
    {
        return $authUser->can('View:PaymentTerms');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentTerms');
    }

    public function update(AuthUser $authUser, PaymentTerms $paymentTerms): bool
    {
        return $authUser->can('Update:PaymentTerms');
    }

    public function delete(AuthUser $authUser, PaymentTerms $paymentTerms): bool
    {
        return $authUser->can('Delete:PaymentTerms');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PaymentTerms');
    }

    public function restore(AuthUser $authUser, PaymentTerms $paymentTerms): bool
    {
        return $authUser->can('Restore:PaymentTerms');
    }

    public function forceDelete(AuthUser $authUser, PaymentTerms $paymentTerms): bool
    {
        return $authUser->can('ForceDelete:PaymentTerms');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PaymentTerms');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PaymentTerms');
    }

    public function replicate(AuthUser $authUser, PaymentTerms $paymentTerms): bool
    {
        return $authUser->can('Replicate:PaymentTerms');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PaymentTerms');
    }

}