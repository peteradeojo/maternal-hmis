<?php

use App\Enums\AncCategory;
use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

const T1 = 0.3;

function authorizedRoutes()
{
    $user = auth()->user();
    $routes = [
        route('dashboard') => ['Dashboard', 'fa-home', null],
    ];

    if ($user->hasRole('doctor')) {
        $routes[route('doctor.history')] = ['History', 'fa-clock', null];
        $routes[route('doctor.admissions')] = ['Wards', 'fa-bed', null];
        $routes[route('doctor.anc-bookings')] = ['Antenatal Appointments', 'fa-book', null];
    }

    if ($user->hasRole('nurse')) {
        $routes[route('nurses.admissions.get')] = ['Admissions', 'fa-bed', null];
        $routes[route('nurses.anc-bookings')] = ['Antenatal Bookings', 'fa-female', null];
    }

    if ($user->hasRole('record')) {
        $routes[route('records.patients')] = ['Patients', 'fa-person', null];
        $routes[route('records.admissions')] = ['Admissions', 'fa-bed', null];
    }

    if ($user->hasRole('admin')) {
        $routes[route('it.staff')] = ['Staff', 'fa-people', null];
        $routes[route('it.wards')] = ['Wards', 'fa-bed', null];
        $routes[route('it.products')] = ['Products', 'fa-item', null];
        $routes[route('it.crm-index')] = ['CRM', 'fa-list', null];
    }

    if ($user->hasRole('lab')) {
        $routes[route('lab.history')] = ['History', 'fa-clock', null];
        $routes[route('lab.admissions')] = ['Admissions', 'fa-bed', null];
        $routes[route('lab.antenatals')] = ['Antenatal Registration', 'fa-heart', null];
    }

    if ($user->hasRole('radiology')) {
        $routes[route('rad.scans')] = ['Scans', 'fa-xray', null];
        $routes[route('rad.history')] = ['History', 'fa-clock', null];
    }

    if ($user->hasRole('pharmacy')) {
        $routes[route('phm.prescriptions')] = ['Prescriptions', 'fa-prescription', null];
        $routes[route('phm.inventory.index')] = ['Inventory', 'fa-warehouse', null];
        $routes[route('phm.admissions')] = ['Wards', 'fa-bed', null];
    }

    if ($user->hasRole('billing')) {
        $routes[route('billing.index')] = ['Billing', 'fa-money-bill-wave', null];
        $routes[route('dis.index')] = ['Prescriptions', 'fa-prescription', null];
        $routes[route('nhi.index')] = ['Patients', 'fa-person', null];
        $routes[route('nhi.encounters')] = ['Encounters', 'fa-walk', null];
    }

    $routes[route('user-profile')] = ['Profile', 'fa-gear', null];
    $routes[route('logout')] = ['Logout', 'fa-sign-out', null];

    return $routes;
}

function ancCardType(int $value)
{
    return (AncCategory::tryFrom($value))?->name ?? 'Unknown';
}

function unslug($str, $process = null)
{
    if ($process) {
        return $process(str_replace(["_"], " ", $str));
    }
    return str_replace(["_"], " ", $str);
}

function unslug_separator($str, $separator = '', $process = null)
{
    if ($separator) {
        $f = explode($separator, $str);
        $str = $f[count($f) - 1];
    }

    if ($process) {
        return $process(str_replace(["_"], " ", $str));
    }
    return str_replace(["_"], " ", $str);
}

function resolve_render($value, $mode = null)
{
    if (!$mode) {
        return $value;
    }

    switch ($mode) {
        case 'datetime':
            return $value->format('Y-m-d h:i A');
    }
}

function notifyUserSuccess(string $message, User|int $user, $options = [])
{
    $options['mode'] ??= AppNotifications::$IN_APP;
    sendUserMessage(['message' => $message, 'bg' => $options['bg'] ?? ['bg-blue-400', 'text-white']], $user, $options);
}

function notifyUserError(string $message, User|int $user, $options = [])
{
    $options['mode'] ??= AppNotifications::$IN_APP;
    sendUserMessage(['message' => $message, 'bg' => ['bg-red-500', 'text-white']], $user, $options);
}

function sendUserMessage($message, User|int $userId, $options = [])
{
    $default = [
        'mode' => AppNotifications::$BOTH,
        ...$options,
    ];

    $message = array_merge($message, ['options' => $default]);
    try {
        Broadcast::private("user." . (is_a($userId, User::class) ? $userId->id : $userId))
            ->as("UserEvent")
            ->with($message)
            ->sendNow();
    } catch (\Exception $e) {
        // report($e);
        logger()->emergency("Error when trying to send notification: " . $e->getMessage());
    }
}

function notifyDepartment($departmentId, $message, $options = [])
{
    $options['mode'] ??= 'both';
    $options['timeout'] ??= 5000;

    if (is_string($message)) {
        $message = ['message' => $message];
    }

    $message = array_merge($message, ['options' => $options]);
    // broadcast(new NotificationSent($departmentId, $message))->toOthers();
    Broadcast::on("department.{$departmentId}")->as('GroupUpdate')->with($message)->sendNow();
}
