<?php

namespace App\Policies;

use App\Models\AntenatalProfile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AntenatalProfilePolicy
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
    public function view(User $user, AntenatalProfile $profile): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse', 'record', 'lab']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'record']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AntenatalProfile $profile): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse', 'record']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AntenatalProfile $profile): bool
    {
        return $user->hasRole('admin');
    }
}
