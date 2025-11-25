@extends('layouts.app')

@section('content')
    <div class="container p-4 bg-white">
        <button onclick="window.print()" class="btn btn-green btn-sm no-print">Print</button>
        <div class="body">
            <x-patient-profile :patient="$visit->patient" />

            <div class="py-2"></div>
        </div>
        <p>Visit id: {{ $visit->id }}</p>

        {{-- @include('doctors.components.history-report', ['visit' => $visit->visit]) --}}

        <x-reports.encounter.vitals :visit="$visit" />
        <x-reports.encounter.examinations :visit="$visit" />
        <x-reports.encounter.notes :visit="$visit" />
        <x-reports.encounter.complaints :visit="$visit" />
        <x-reports.encounter.diagnoses :visit="$visit" />
        <x-reports.encounter.tests :visit="$visit" />
        <x-reports.encounter.scans :visit="$visit" />
        <x-reports.encounter.prescriptions :visit="$visit" />
        <x-reports.encounter.admission :visit="$visit" />
    </div>
@endsection
