<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\InventoryAdjustment;
use App\Models\User;

class InventoryAdjustmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any InventoryAdjustment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InventoryAdjustment $inventoryadjustment): bool
    {
        return $user->checkPermissionTo('view InventoryAdjustment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create InventoryAdjustment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InventoryAdjustment $inventoryadjustment): bool
    {
        return $user->checkPermissionTo('update InventoryAdjustment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InventoryAdjustment $inventoryadjustment): bool
    {
        return $user->checkPermissionTo('delete InventoryAdjustment');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InventoryAdjustment $inventoryadjustment): bool
    {
        return $user->checkPermissionTo('restore InventoryAdjustment');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InventoryAdjustment $inventoryadjustment): bool
    {
        return $user->checkPermissionTo('force-delete InventoryAdjustment');
    }
}
