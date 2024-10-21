@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">
            Admission plan for: {{ $visit->patient->name }}
        </div>

        <div class="body">
            <div class="py-4">
                <livewire:admissions.plan :visit="$visit->visit" />
            </div>
        </div>

    </div>
@endsection
