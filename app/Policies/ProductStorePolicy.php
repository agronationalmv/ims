<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ProductStore;
use App\Models\User;

class ProductStorePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ProductStore');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductStore $productstore): bool
    {
        return $user->checkPermissionTo('view ProductStore');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ProductStore');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductStore $productstore): bool
    {
        return $user->checkPermissionTo('update ProductStore');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductStore $productstore): bool
    {
        return $user->checkPermissionTo('delete ProductStore');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductStore $productstore): bool
    {
        return $user->checkPermissionTo('restore ProductStore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductStore $productstore): bool
    {
        return $user->checkPermissionTo('force-delete ProductStore');
    }
}
