<?php

use App\Enums\Department;

function departmentRoutes()
{
    $base = [
        route('logout') => 'Logout',
    ];

    // $all = [];
    $doctors = array_merge(
        [
            route('doctor.patients') => 'Patients',
        ],
        $base,
    );

    $nurses = array_merge([
        route('nurses.vitals') => 'Vitals',
    ], $base);

    $records = array_merge([
        route('records.patients') => 'Patients',
    ], $base);

    return [
        Department::DOC->value => $doctors,
        Department::NUR->value => $nurses,
        Department::REC->value => $records,
    ];
}
