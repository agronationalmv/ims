<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\PoReceiveDetail;
use App\Models\User;

class PoReceiveDetailPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any PoReceiveDetail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PoReceiveDetail $poreceivedetail): bool
    {
        return $user->checkPermissionTo('view PoReceiveDetail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create PoReceiveDetail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PoReceiveDetail $poreceivedetail): bool
    {
        return $user->checkPermissionTo('update PoReceiveDetail');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PoReceiveDetail $poreceivedetail): bool
    {
        return $user->checkPermissionTo('delete PoReceiveDetail');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PoReceiveDetail $poreceivedetail): bool
    {
        return $user->checkPermissionTo('restore PoReceiveDetail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PoReceiveDetail $poreceivedetail): bool
    {
        return $user->checkPermissionTo('force-delete PoReceiveDetail');
    }
}
