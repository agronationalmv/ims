<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ExpenseAccount;
use App\Models\User;

class ExpenseAccountPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ExpenseAccount');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExpenseAccount $expenseaccount): bool
    {
        return $user->checkPermissionTo('view ExpenseAccount');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ExpenseAccount');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExpenseAccount $expenseaccount): bool
    {
        return $user->checkPermissionTo('update ExpenseAccount');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExpenseAccount $expenseaccount): bool
    {
        return $user->checkPermissionTo('delete ExpenseAccount');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExpenseAccount $expenseaccount): bool
    {
        return $user->checkPermissionTo('restore ExpenseAccount');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExpenseAccount $expenseaccount): bool
    {
        return $user->checkPermissionTo('force-delete ExpenseAccount');
    }
}
