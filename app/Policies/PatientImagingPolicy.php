<?php

namespace App\Policies;

use App\Models\PatientImaging;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientImagingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse', 'record', 'radiology', 'billing']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PatientImaging $scan): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse', 'record', 'radiology', 'billing']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse', 'record']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PatientImaging $scan): bool
    {
        return $user->hasAnyRole(['admin', 'radiology']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PatientImaging $scan): bool
    {
        return $user->hasRole('admin');
    }
}
