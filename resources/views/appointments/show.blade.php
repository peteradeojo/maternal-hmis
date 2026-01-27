@extends('layouts.app')
@section('title', 'Appointment | ' . $appointment->patient->name)

@section('content')
    <div class="container card p-2">
        <x-patient-profile :patient="$appointment->patient" />
    </div>
@endsection
