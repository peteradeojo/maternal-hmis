<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdmissionPolicy
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
    public function view(User $user, Admission $admission): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse', 'record', 'pharmacy']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Admission $admission): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Admission $admission): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Admission $admission): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Admission $admission): bool
    {
        return $user->hasRole('admin');
    }
}
