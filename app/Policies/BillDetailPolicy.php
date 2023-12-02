<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\BillDetail;
use App\Models\User;

class BillDetailPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any BillDetail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BillDetail $billdetail): bool
    {
        return $user->checkPermissionTo('view BillDetail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create BillDetail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BillDetail $billdetail): bool
    {
        return $user->checkPermissionTo('update BillDetail');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BillDetail $billdetail): bool
    {
        return $user->checkPermissionTo('delete BillDetail');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BillDetail $billdetail): bool
    {
        return $user->checkPermissionTo('restore BillDetail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BillDetail $billdetail): bool
    {
        return $user->checkPermissionTo('force-delete BillDetail');
    }
}
