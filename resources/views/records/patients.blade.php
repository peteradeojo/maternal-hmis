@extends('layouts.app')
@section('title', 'Patients')

@section('content')
    <div class="container">
        <div class="card py px mb-1">
            <h2>Register Patient</h2>

            <div class="py">
                <a href="{{ route('records.patients.new') }}">General Patient</a>
                <a href="{{ route('records.patients.new') }}?mode=anc" class="pl-1">Antenatal Patient</a>
            </div>
        </div>

        <div class="card py px">
            <h2 class="my">Patients</h2>
            <table id="patients" class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Card Number</th>
                        <th>Gender</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/records/patients.js'])
@endpush
