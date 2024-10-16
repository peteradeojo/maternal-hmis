@extends('layouts.app')
@section('title', 'Patients')

@section('content')
    <div class="container">
        <div class="card py px mb-1">
            <h2>Register Patient</h2>

            <div class="py flex gap-x-3">
                <a href="{{ route('records.patients.new') }}" class="btn bg-green-500">General Patient</a>
                <a href="{{ route('records.patients.new') }}?mode=anc" class="btn bg-red-500  text-white">Antenatal Patient</a>
            </div>
        </div>

        <div class="card py px">
            <h2 class="my">Patients</h2>

            <livewire:records.patient-search />
        </div>
    </div>
@endsection
