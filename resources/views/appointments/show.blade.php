@extends('layouts.app')
@section('title', 'Appointment | ' . $appointment->patient->name)

@section('content')
    <div class="grid gap-y-6">
        <div class="container card p-2">
            <x-patient-profile :patient="$appointment->patient">
                <p><b>Booked by</b>: {{ $appointment->source }}</p>
            </x-patient-profile>
        </div>

        <div class="container card p-2">
            <h3 class="header text-xl font-semibold">Appointment Details</h3>

            <div class="grid grid-cols-3">
                <p><b>Date & Time:</b> {{ $appointment->appointment_date?->format("gA \o\\n Y-m-d") }}</p>
                <p></p>
                <p><b>Booked by:</b> {{ $appointment->user->name }}</p>
            </div>

            @component('components.reports.encounter.notes-list', ['notes' => $appointment->notes])
            @endcomponent

            @can('inspect', $appointment)
                <x-reports.encounter.complaints :visit="$appointment->source_visit" />
                <x-reports.encounter.examinations :visit="$appointment->source_visit" />
                <x-reports.encounter.notes :visit="$appointment->source_visit" />
                <x-reports.encounter.diagnoses :visit="$appointment->source_visit" />
                <x-reports.encounter.tests :visit="$appointment->source_visit" />
                <x-reports.encounter.scans :visit="$appointment->source_visit" />
                <x-reports.encounter.prescriptions :visit="$appointment->source_visit" />
                <x-reports.encounter.admission :visit="$appointment->source_visit" />
            @endcan

            @can('begin_appointment', $appointment)
                <button class="btn bg-primary text-white">Start appointment</button>
            @endcan
        </div>
    </div>
@endsection
