<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\OrderDetail;
use App\Models\User;

class OrderDetailPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any OrderDetail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderDetail $orderdetail): bool
    {
        return $user->checkPermissionTo('view OrderDetail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create OrderDetail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderDetail $orderdetail): bool
    {
        return $user->checkPermissionTo('update OrderDetail');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderDetail $orderdetail): bool
    {
        return $user->checkPermissionTo('delete OrderDetail');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderDetail $orderdetail): bool
    {
        return $user->checkPermissionTo('restore OrderDetail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderDetail $orderdetail): bool
    {
        return $user->checkPermissionTo('force-delete OrderDetail');
    }
}
