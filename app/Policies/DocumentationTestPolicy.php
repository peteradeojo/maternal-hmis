<?php

namespace App\Policies;

use App\Models\DocumentationTest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentationTestPolicy
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
    public function view(User $user, DocumentationTest $test): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'nurse', 'record', 'lab', 'radiology']);
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
    public function update(User $user, DocumentationTest $test): bool
    {
        return $user->hasAnyRole(['admin', 'lab', 'radiology']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentationTest $test): bool
    {
        return $user->hasRole('admin');
    }
}
