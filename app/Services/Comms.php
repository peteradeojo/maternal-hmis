<?php

namespace App\Services;

use App\Enums\AppNotifications;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

class Comms
{
    static function notifyUserSuccess(string $message, User|int $user, $options = [])
    {
        $options['mode'] ??= AppNotifications::$IN_APP;
        sendUserMessage(['message' => $message, 'bg' => ['bg-blue-400', 'text-white']], $user, $options);
    }

    static function notifyUserError(string $message, User|int $user, $options = [])
    {
        $options['mode'] ??= AppNotifications::$IN_APP;
        sendUserMessage(['message' => $message, 'bg' => ['bg-red-500', 'text-white']], $user, $options);
    }

    static function sendUserMessage($message, User|int $userId, $options = [])
    {
        $options['mode'] ??= AppNotifications::$BOTH;

        $message = array_merge($message, ['options' => $options]);
        try {
            Broadcast::private("user." . (is_a($userId, User::class) ? $userId->id : $userId))
                ->as("UserEvent")
                ->with($message)
                ->sendNow();
        } catch (\Exception $e) {
            logger()->emergency("Error when trying to send notification: " . $e->getMessage());
        }
    }

    static function notifyDepartment($departmentId, $message, $options = [])
    {
        $options['mode'] ??= 'both';
        $options['timeout'] ??= 5000;

        if (is_string($message)) {
            $message = ['message' => $message];
        }

        $message = array_merge($message, ['options' => $options]);
        Broadcast::on("department.{$departmentId}")->as('GroupUpdate')->with($message)->sendNow();
    }
}
