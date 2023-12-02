<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PoReceive;
use App\Models\User;

class PoReceivePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PoReceive');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PoReceive $poreceive): bool
    {
        return $user->checkPermissionTo('view PoReceive');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PoReceive');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PoReceive $poreceive): bool
    {
        return $user->checkPermissionTo('update PoReceive');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PoReceive $poreceive): bool
    {
        return $user->checkPermissionTo('delete PoReceive');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PoReceive $poreceive): bool
    {
        return $user->checkPermissionTo('restore PoReceive');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PoReceive $poreceive): bool
    {
        return $user->checkPermissionTo('force-delete PoReceive');
    }
}
