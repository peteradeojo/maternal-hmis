@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">
            <p>{{ $admission->patient->name }} (Admitted: {{ $admission->created_at->format('Y-m-d') }})</p>
        </div>
        <div class="header">
            <p><b>Patient:</b> {{ $admission->patient->name }}</p>
            <p><b>Age:</b> {{ $admission->patient->dob?->diffInYears() }}</p>
            <p><b>Gender:</b> {{ $admission->patient->gender_value }}</p>
            <p><b>Ward:</b> {{ $admission->ward->name }}</p>
            <p><b>Being Managed for:</b> {{ $admission->admittable->complaints }}</p>
        </div>
    </div>
@endsection
