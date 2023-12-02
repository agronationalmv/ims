<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\AdjustmentDetail;
use App\Models\User;

class AdjustmentDetailPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any AdjustmentDetail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdjustmentDetail $adjustmentdetail): bool
    {
        return $user->checkPermissionTo('view AdjustmentDetail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create AdjustmentDetail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdjustmentDetail $adjustmentdetail): bool
    {
        return $user->checkPermissionTo('update AdjustmentDetail');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdjustmentDetail $adjustmentdetail): bool
    {
        return $user->checkPermissionTo('delete AdjustmentDetail');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AdjustmentDetail $adjustmentdetail): bool
    {
        return $user->checkPermissionTo('restore AdjustmentDetail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AdjustmentDetail $adjustmentdetail): bool
    {
        return $user->checkPermissionTo('force-delete AdjustmentDetail');
    }
}
