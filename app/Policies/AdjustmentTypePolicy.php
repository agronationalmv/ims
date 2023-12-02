<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\AdjustmentType;
use App\Models\User;

class AdjustmentTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any AdjustmentType');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdjustmentType $adjustmenttype): bool
    {
        return $user->checkPermissionTo('view AdjustmentType');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create AdjustmentType');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdjustmentType $adjustmenttype): bool
    {
        return $user->checkPermissionTo('update AdjustmentType');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdjustmentType $adjustmenttype): bool
    {
        return $user->checkPermissionTo('delete AdjustmentType');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AdjustmentType $adjustmenttype): bool
    {
        return $user->checkPermissionTo('restore AdjustmentType');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AdjustmentType $adjustmenttype): bool
    {
        return $user->checkPermissionTo('force-delete AdjustmentType');
    }
}
