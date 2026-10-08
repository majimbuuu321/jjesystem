<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InvoiceHeader;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceHeaderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InvoiceHeader');
    }

    public function view(AuthUser $authUser, InvoiceHeader $invoiceHeader): bool
    {
        return $authUser->can('View:InvoiceHeader');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InvoiceHeader');
    }

    public function update(AuthUser $authUser, InvoiceHeader $invoiceHeader): bool
    {
        return $authUser->can('Update:InvoiceHeader');
    }

    public function delete(AuthUser $authUser, InvoiceHeader $invoiceHeader): bool
    {
        return $authUser->can('Delete:InvoiceHeader');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InvoiceHeader');
    }

    public function restore(AuthUser $authUser, InvoiceHeader $invoiceHeader): bool
    {
        return $authUser->can('Restore:InvoiceHeader');
    }

    public function forceDelete(AuthUser $authUser, InvoiceHeader $invoiceHeader): bool
    {
        return $authUser->can('ForceDelete:InvoiceHeader');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InvoiceHeader');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InvoiceHeader');
    }

    public function replicate(AuthUser $authUser, InvoiceHeader $invoiceHeader): bool
    {
        return $authUser->can('Replicate:InvoiceHeader');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InvoiceHeader');
    }

}