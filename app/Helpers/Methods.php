<?php

use App\Enums\AncCategory;
use App\Enums\AppNotifications;
use App\Enums\Department;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

function departmentRoutes()
{
    $base = [
        route('user-profile') => ['Profile', 'fa-gear'],
        route('logout') => ['Logout', 'fa-sign-out'],
    ];

    $doctors =
        [
            route('doctor.history') => ['History', 'fa-clock'],
            route('doctor.admissions') => ['Wards', 'fa-bed'],
            route('doctor.anc-bookings') => ['Antenatal Bookings', 'fa-book'],
        ];

    $nurses = [
        // route('nurses.vitals') => 'Vitals',
        route('nurses.admissions.get') => ['Admissions', 'fa-bed'],
        route('nurses.anc-bookings') => ['Antenatal Bookings', 'fa-female'],
    ];

    $records = [
        route('records.patients') => ['Patients', 'fa-person'],
        route('billing.index') => ['Billing', 'fa-money-bill-wave'],
        // route('records.history') => 'Visit History',
        route('records.admissions') => ['Admissions', 'fa-bed'],
    ];

    $it = [
        route('it.staff') => ['Staff', 'fa-people'],
        route('it.wards') => ['Wards', 'fa-bed'],
        route('it.products') => ['Products', 'fa-item'],
        route('it.crm-index') => ['CRM', 'fa-list'],
    ];

    $lab = [
        route('lab.history') => ['History', 'fa-clock'],
        route('lab.admissions') => ['Admissions', 'fa-bed'],
        route('lab.antenatals') => ['Antenatal Registration', 'fa-heart'],
    ];

    $rad = [
        route('rad.scans') => ['Scans', 'fa-xray'],
        route('rad.history') => ['History', 'fa-clock'],
    ];

    $phm = [
        route('phm.prescriptions') => ['Presciptions', 'fa-prescription'],
    ];

    $dis = [
        route('dis.index') => ['Prescriptions', 'fa-prescription'],
    ];

    $nhi = [
        route('nhi.index') => ['Patients', 'fa-person'],
        route('nhi.encounters') => ['Encounters', 'fa-walk'],
    ];

    $dashboard = [
        route('dashboard') => ['Dashboard', 'fa-home'],
    ];

    return [
        Department::DOC->value => array_merge($dashboard, $doctors, $base),
        Department::NUR->value => array_merge($dashboard, $nurses, $base),
        Department::REC->value => array_merge($dashboard, $records, $base),
        Department::IT->value => array_merge($dashboard, $it, $base),
        Department::LAB->value => array_merge($dashboard, $lab, $base),
        Department::RAD->value => array_merge($dashboard, $rad, $base),
        Department::PHA->value => array_merge($dashboard, $phm, $base),
        Department::DIS->value => array_merge($dashboard, $dis, $base),
        Department::NHI->value => array_merge($dashboard, $nhi, $base),
    ];
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
    sendUserMessage(['message' => $message, 'bg' => ['bg-blue-400', 'text-white']], $user, $options);
}

function notifyUserError(string $message, User|int $user, $options = [])
{
    $options['mode'] ??= AppNotifications::$IN_APP;
    sendUserMessage(['message' => $message, 'bg' => ['bg-red-500', 'text-white']], $user, $options);
}

function sendUserMessage($message, User|int $userId, $options = [])
{
    $options['mode'] ??= AppNotifications::$BOTH;

    $message = array_merge($message, ['options' => $options]);
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

    if (is_string($message)) {
        $message = ['message' => $message];
    }

    $message = array_merge($message, ['options' => $options]);
    // broadcast(new NotificationSent($departmentId, $message))->toOthers();
    Broadcast::on("department.{$departmentId}")->as('GroupUpdate')->with($message)->sendNow();
}
