<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Auth\Access\Response;

class VisitPolicy
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
    public function view(User $user, Visit $visit): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('doctor') && $visit->consultant_id === $user->id) {
            return true;
        }

        if ($user->hasRole('nurse') && ($visit->awaiting_vitals || $visit->awaiting_doctor)) {
            return true;
        }

        if ($user->hasRole('lab') && $visit->awaiting_lab_results) {
            return true;
        }

        if ($user->hasRole('radiology') && $visit->awaiting_radiology) {
            return true;
        }

        if ($user->hasRole('pharmacy') && $visit->awaiting_pharmacy) {
            return true;
        }

        if ($user->hasRole('billing')) {
            return true; // Billing usually needs to see all visits to process payments
        }

        if ($user->hasRole('record')) {
            return true; // Records staff manage all visits
        }

        return false;
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
    public function update(User $user, Visit $visit): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('doctor') && $visit->consultant_id === $user->id) {
            return true;
        }

        if ($user->hasRole('record')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Visit $visit): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Visit $visit): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Visit $visit): bool
    {
        return $user->hasRole('admin');
    }
}
