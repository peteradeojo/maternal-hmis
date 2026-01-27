<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BillPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse', 'record', 'pharmacy', 'lab', 'radiology', 'billing']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bill $bill): bool
    {
        return $user->hasAnyRole(['admin', 'billing', 'record', 'pharmacy', 'lab', 'radiology']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'billing', 'record']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bill $bill): bool
    {
        return $user->hasAnyRole(['admin', 'billing']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bill $bill): bool
    {
        return $user->hasRole(['admin', 'finance']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bill $bill): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bill $bill): bool
    {
        return $user->hasRole('admin');
    }
}
