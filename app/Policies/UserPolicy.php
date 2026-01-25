<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function change_status(User $user_one, User $staff) {
        return $user->can(Permissions::MANAGE_USERS->value) && $user_one->is($staff) == false;
    }
}
