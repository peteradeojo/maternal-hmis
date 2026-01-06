<?php

use App\Enums\AncCategory;
use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Enums\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

const T1 = 0.3;

function getRouteMap()
{
    // $routeMap = [
    //     'doctor' => [
    //         route('doctor.history') => ['History', 'fa-clock', null],
    //         route('doctor.admissions') => ['Wards', 'fa-bed', null],
    //         route('doctor.anc-bookings') => ['Antenatal Appointments', 'fa-book', null],
    //     ],
    //     'nurse' => [
    //         route('nurses.admissions.get') => ['Admissions', 'fa-bed', null],
    //         route('nurses.anc-bookings') => ['Antenatal Bookings', 'fa-female', null],
    //     ],
    //     'record' => [
    //         route('records.patients') => ['Patients', 'fa-person', null],
    //         route('records.admissions') => ['Admissions', 'fa-bed', null],
    //     ],
    //     'admin' => [
    //         route('it.staff') => ['Staff', 'fa-people', null],
    //         route('it.wards') => ['Wards', 'fa-bed', null],
    //         route('it.products') => ['Products', 'fa-item', null],
    //         route('it.crm-index') => ['CRM', 'fa-list', null],
    //         route('iam.index') => ['IAM', 'fa-shield-halved', null],
    //     ],
    //     'lab' => [
    //         route('lab.history') => ['History', 'fa-clock', null],
    //         route('lab.admissions') => ['Admissions', 'fa-bed', null],
    //         route('lab.antenatals') => ['Antenatal Registration', 'fa-heart', null],
    //     ],
    //     'radiology' => [
    //         route('rad.scans') => ['Scans', 'fa-xray', null],
    //         route('rad.history') => ['History', 'fa-clock', null],
    //     ],
    //     'pharmacy' => [
    //         route('phm.prescriptions') => ['Prescriptions', 'fa-prescription', null],
    //         route('phm.inventory.index') => ['Inventory', 'fa-warehouse', null],
    //         route('phm.admissions') => ['Wards', 'fa-bed', null],
    //     ],
    //     'billing' => [
    //         route('billing.index') => ['Billing', 'fa-money-bill-wave', null],
    //         // route('dis.index') => ['Prescriptions', 'fa-prescription', null],
    //         route('nhi.index') => ['Patients', 'fa-person', null],
    //         route('nhi.encounters') => ['Encounters', 'fa-walk', null],
    //     ],
    //     'media' => [
    //         route('it.crm-index') => ['CRM', 'fa-list', null],
    //     ],
    //     'support' => [
    //         route('it.staff') => ['Staff', 'fa-person'],
    //         route('it.wards') => ['Wards', 'fa-bed'],
    //         route('it.products') => ['Products', 'fa-item'],
    //     ],
    // ];

    $routeMap = [
        [
            'role' => 'doctor',
            'label' => ['Consulting', 'fa-stethoscope'],
            'routes' => [
                'History' => [route('doctor.history'), 'fa-clock', null],
                'Wards' => [route('doctor.admissions'), 'fa-bed', null],
                'Antenatal Appointments' => [route('doctor.anc-bookings'), 'fa-book', null],
            ],
        ],
        [
            'role' => 'nurse',
            'label' => ['Nursing', 'fa-user-nurse'],
            'routes' => [
                'Admissions' => [route('nurses.admissions.get'), 'fa-bed', null],
                'Antenatal Bookings' => [route('nurses.anc-bookings'), 'fa-female', null],
            ],
        ],
        [
            'role' => 'record',
            'label' => ['Records', 'fa-folder-open'],
            'routes' => [
                'Patients' => [route('records.patients'), 'fa-person', null],
                'Admissions' => [route('records.admissions'), 'fa-bed', null],
            ],
        ],
        [
            'role' => 'admin',
            'label' => ['Administration', 'fa-user-tie'],
            'routes' => [
                'Staff' => [route('it.staff'), 'fa-people', null],
                'Wards' => [route('it.wards'), 'fa-bed', null],
                'Products' => [route('it.products'), 'fa-item', null],
                'CRM' => [route('it.crm-index'), 'fa-list', null],
                'IAM' => [route('iam.index'), 'fa-shield-halved', null],
            ],
        ],
        [
            'role' => 'lab',
            'label' => ['Laboratory', 'fa-flask'],
            'routes' => [
                'History' => [route('lab.history'), 'fa-clock', null],
                'Admissions' => [route('lab.admissions'), 'fa-bed', null],
                'Antenatal Registration' => [route('lab.antenatals'), 'fa-heart', null],
            ],
        ],
        [
            'role' => 'radiology',
            'label' => ['Radiology', 'fa-x-ray'],
            'routes' => [
                'Scans' => [route('rad.scans'), 'fa-x-ray', null],
                'History' => [route('rad.history'), 'fa-clock', null],
            ],
        ],
        [
            'role' => 'pharmacy',
            'label' => ['Pharmacy', 'fa-pills'],
            'routes' => [
                'Prescriptions' => [route('phm.prescriptions'), 'fa-prescription', null],
                'Inventory' => [route('phm.inventory.index'), 'fa-warehouse', null],
                'Wards' => [route('phm.admissions'), 'fa-bed', null],
            ],
        ],
        [
            'role' => 'billing',
            'label' => ['Billing & NHI', 'fa-file-invoice-dollar'],
            'routes' => [
                'Billing' => [route('billing.index'), 'fa-money-bill-wave', null],
                'Patients' => [route('nhi.index'), 'fa-person', null],
                'Encounters' => [route('nhi.encounters'), 'fa-walk', null],
            ],
        ],
        [
            'role' => 'media',
            'label' => ['Media/CRM', 'fa-photo-video'],
            'routes' => [
                'CRM' => [route('it.crm-index'), 'fa-list', null],
            ],
        ],
        [
            'role' => 'support',
            'label' => ['Support', 'fa-headset'],
            'routes' => [
                'Staff' => [route('it.staff'), 'fa-person'],
                'Wards' => [route('it.wards'), 'fa-bed'],
                'Products' => [route('it.products'), 'fa-item'],
            ],
        ],
    ];

    return $routeMap;
}

function authorizedRoutes()
{
    $routeMap = getRouteMap();

    $user = auth()->user();
    $routes = [];

    foreach ($routeMap as $map) {
        if ($user->hasAnyRole('superadmin', $map['role'])) {
            unset($map['role']);
            $routes[] = $map;
        }
    }

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
