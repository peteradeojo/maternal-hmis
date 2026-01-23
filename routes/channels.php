<?php

use App\Enums\Department;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('department.{departmentId}', function (User $user, int $departmentId) {
    return $user->department_id === $departmentId;
});

Broadcast::channel('role.{roleName}', function (User $user, string $role) {
    return $user->hasRole($role);
});

Broadcast::channel('logs', function (User $user) {
    return $user->department_id === Department::IT->value;
});

Broadcast::channel('user.{userId}', fn($user, int $id) => (int) $user->id == $id);
