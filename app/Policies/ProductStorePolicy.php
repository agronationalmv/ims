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
        return $user->can('view_any_store');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductStore $productstore): bool
    {
        return $user->can('view_store');

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_store');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductStore $productstore): bool
    {
        return $user->can('update_store');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductStore $productstore): bool
    {
        return $user->can('delete_store');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductStore $productstore): bool
    {
        return $user->can('restore_store');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductStore $productstore): bool
    {
        return $user->can('force_delete_store');
    }
    
}
