@extends('layouts.app')
@section('title', $admission->patient->name)

@section('content')
    <div class="container card p-3">
        <x-patient-profile :patient="$admission->patient" />

        <livewire:lab.manage-tests :visit="$admission" />
    </div>
@endsection
