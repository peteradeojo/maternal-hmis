<?php

use App\Enums\AncCategory;
use App\Enums\Department;

function departmentRoutes()
{
    $base = [
    ];

    $doctors = array_merge(
        [
            route('doctor.patients') => 'Patients',
            route('doctor.anc-bookings') => 'Antenatal Bookings',
        ],
        $base,
    );

    $nurses = array_merge([
        route('nurses.vitals') => 'Vitals',
        route('nurses.anc-bookings') => 'Antenatal Bookings',
    ], $base);

    $records = array_merge([
        route('records.patients') => 'Patients',
    ], $base);

    $it = array_merge([
        route('it.staff') => 'Staff',
    ], $base);

    $lab = array_merge([
        route('lab.history') => 'History',
        route('lab.antenatals') => 'Antenatals',
    ], $base);

    $rad = array_merge([
        route('rad.scans') => 'Scans',
    ], $base);

    $phm = array_merge([
        route('phm.prescriptions') => 'Presciptions'
    ], $base);

    $dis = array_merge([
        route('dis.index') => 'Prescriptions'
    ], $base);

    return [
        Department::DOC->value => $doctors,
        Department::NUR->value => $nurses,
        Department::REC->value => $records,
        Department::IT->value => $it,
        Department::LAB->value => $lab,
        Department::RAD->value => $rad,
        Department::PHA->value => $phm,
        Department::DIS->value => $dis,
    ];
}


function ancCardType(int $value)
{
    return (AncCategory::tryFrom($value))?->name ?? 'Unknown';
}
