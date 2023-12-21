<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PurchaseRequestDetail;
use App\Models\User;

class PurchaseRequestDetailPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PurchaseRequestDetail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PurchaseRequestDetail $purchaserequestdetail): bool
    {
        return $user->checkPermissionTo('view PurchaseRequestDetail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PurchaseRequestDetail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PurchaseRequestDetail $purchaserequestdetail): bool
    {
        return $user->checkPermissionTo('update PurchaseRequestDetail');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PurchaseRequestDetail $purchaserequestdetail): bool
    {
        return $user->checkPermissionTo('delete PurchaseRequestDetail');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PurchaseRequestDetail $purchaserequestdetail): bool
    {
        return $user->checkPermissionTo('restore PurchaseRequestDetail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PurchaseRequestDetail $purchaserequestdetail): bool
    {
        return $user->checkPermissionTo('force-delete PurchaseRequestDetail');
    }
}
