@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- <p class="h1 text-white">Hello</p> --}}
        <div class="card p-3">
            <div class="header">
                Admission plan for: {{ $admission->visit->patient->name }}
            </div>

            <div class="body">
                <div class="py-4">
                    <livewire:admissions.plan :visit="$admission->visit->visit" :admission="$admission" />
                </div>
            </div>
        </div>
    </div>
@endsection
