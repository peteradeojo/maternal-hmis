@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="container">
        @livewire('dashboard.patient-stats', ['user' => $user])

        <div class="card">
            @if ($user->department_id == 1)
                @livewire('doctor.waiting-patients', ['user' => $user])
            @endif

            @if ($user->department_id == 2)
                @livewire('nursing.vital-list')
            @endif
        </div>
    </div>
@endsection
