<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\StoreType;
use App\Models\User;

class StoreTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any StoreType');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StoreType $storetype): bool
    {
        return $user->checkPermissionTo('view StoreType');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create StoreType');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StoreType $storetype): bool
    {
        return $user->checkPermissionTo('update StoreType');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StoreType $storetype): bool
    {
        return $user->checkPermissionTo('delete StoreType');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StoreType $storetype): bool
    {
        return $user->checkPermissionTo('restore StoreType');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StoreType $storetype): bool
    {
        return $user->checkPermissionTo('force-delete StoreType');
    }
}
