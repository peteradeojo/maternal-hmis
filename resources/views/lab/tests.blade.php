@extends('layouts.app')
@section('title', "#{$visit->id} | Tests")

@section('content')
    <div class="grid gap-y-4">
        <div>
            <x-back-link />
            <div class="bg-white p-2">
                <x-patient-profile :patient="$visit->patient" />
            </div>
        </div>

        <div class="bg-white p-2">
            <p class="text-lg font-semibold pb-4">ID: #{{ $visit->id }}</p>

            <livewire:lab.manage-tests :visit="$visit" />
        </div>
    </div>
@endsection
