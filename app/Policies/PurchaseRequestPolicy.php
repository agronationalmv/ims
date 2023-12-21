<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PurchaseRequest;
use App\Models\User;

class PurchaseRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PurchaseRequest');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PurchaseRequest $purchaserequest): bool
    {
        return $user->checkPermissionTo('view PurchaseRequest');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PurchaseRequest');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PurchaseRequest $purchaserequest): bool
    {
        return $user->checkPermissionTo('update PurchaseRequest');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PurchaseRequest $purchaserequest): bool
    {
        return $user->checkPermissionTo('delete PurchaseRequest');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PurchaseRequest $purchaserequest): bool
    {
        return $user->checkPermissionTo('restore PurchaseRequest');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PurchaseRequest $purchaserequest): bool
    {
        return $user->checkPermissionTo('force-delete PurchaseRequest');
    }
}
