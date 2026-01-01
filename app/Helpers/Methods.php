<?php

use App\Enums\AncCategory;
use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

const T1 = 0.3;

function getRouteMap()
{
    $routeMap = [
        'doctor' => [
            route('doctor.history') => ['History', 'fa-clock', null],
            route('doctor.admissions') => ['Wards', 'fa-bed', null],
            route('doctor.anc-bookings') => ['Antenatal Appointments', 'fa-book', null],
        ],
        'nurse' => [
            route('nurses.admissions.get') => ['Admissions', 'fa-bed', null],
            route('nurses.anc-bookings') => ['Antenatal Bookings', 'fa-female', null],
        ],
        'record' => [
            route('records.patients') => ['Patients', 'fa-person', null],
            route('records.admissions') => ['Admissions', 'fa-bed', null],
        ],
        'admin' => [
            route('it.staff') => ['Staff', 'fa-people', null],
            route('it.wards') => ['Wards', 'fa-bed', null],
            route('it.products') => ['Products', 'fa-item', null],
            route('it.crm-index') => ['CRM', 'fa-list', null],
            route('iam.index') => ['IAM', 'fa-shield-halved', null],
        ],
        'lab' => [
            route('lab.history') => ['History', 'fa-clock', null],
            route('lab.admissions') => ['Admissions', 'fa-bed', null],
            route('lab.antenatals') => ['Antenatal Registration', 'fa-heart', null],
        ],
        'radiology' => [
            route('rad.scans') => ['Scans', 'fa-xray', null],
            route('rad.history') => ['History', 'fa-clock', null],
        ],
        'pharmacy' => [
            route('phm.prescriptions') => ['Prescriptions', 'fa-prescription', null],
            route('phm.inventory.index') => ['Inventory', 'fa-warehouse', null],
            route('phm.admissions') => ['Wards', 'fa-bed', null],
        ],
        'billing' => [
            route('billing.index') => ['Billing', 'fa-money-bill-wave', null],
            // route('dis.index') => ['Prescriptions', 'fa-prescription', null],
            route('nhi.index') => ['Patients', 'fa-person', null],
            route('nhi.encounters') => ['Encounters', 'fa-walk', null],
        ],
        'media' => [
            route('it.crm-index') => ['CRM', 'fa-list', null],
        ],
        'support' => [
            route('it.staff') => ['Staff', 'fa-person'],
            route('it.wards') => ['Wards', 'fa-bed'],
            route('it.products') => ['Products', 'fa-item'],
        ],
    ];
    return $routeMap;
}

function authorizedRoutes()
{
    $routeMap = getRouteMap();

    $user = auth()->user();
    $routes = [
        route('dashboard') => ['Dashboard', 'fa-home', null],
    ];

    // if ($user->hasRole('doctor')) {
    //     $routes = array_merge($routes, $routeMap['doctor']);
    // }

    // if ($user->hasRole('nurse')) {
    //     $routes = array_merge($routes, $routeMap['nurse']);
    // }

    // if ($user->hasRole('record')) {
    //     $routes = array_merge($routes, $routeMap['record']);
    // }

    // if ($user->hasRole('lab')) {
    //     $routes = array_merge($routes, $routeMap['lab']);
    // }

    // if ($user->hasRole('radiology')) {
    //     $routes = array_merge($routes, $routeMap['radiology']);
    // }

    // if ($user->hasRole('pharmacy')) {
    //     $routes = array_merge($routes, $routeMap['pharmacy']);
    // }

    // if ($user->hasRole('billing')) {
    //     $routes = array_merge($routes, $routeMap['billing']);
    // }

    // if ($user->hasRole('admin')) {
    //     // foreach ($routeMap as $role => $rM) {
    //     //     $routes = array_merge($routes, $rM);
    //     // }
    //     $routes = array_merge($routes, $routeMap['admin']);
    // }

    foreach ($routeMap as $role => $map) {
        if ($user->hasRole($role)) {
            $routes = array_merge($routes, $map);
        }
    }

    $uniqRoutes = [];
    $uniqRoutes = array_unique(array_keys($routes));

    $routes = array_combine($uniqRoutes, array_values($routes));

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
