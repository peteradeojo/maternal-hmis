@extends('layouts.app')
@section('title', $profile->patient->name)

@section('content')
    <div class="p-3 bg-white">
        <div class="header">
            <x-patient-profile :patient="$profile->patient" />
        </div>

        <div class="body p-2">
            @foreach ($errors->all() as $error)
                <div class="p-1 bg-red text-white">{{ $error }}</div>
            @endforeach

            <div class="p-2">
                @foreach ($profile->tests as $test)
                    <livewire:lab.test :test="$test" />
                @endforeach
            </div>
        </div>
    </div>
@endsection
