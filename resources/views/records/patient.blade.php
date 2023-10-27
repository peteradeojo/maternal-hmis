@extends('layouts.app')
@section('title', $patient->name)

@php
    $btnColor = 'btn btn-red';
@endphp

@section('content')
    <div class="card py px mb-1 foldable">
        <div class="header foldable-header">
            <div class="row between">
                <p class="card-header">Biodata</p>
                <a href="#" id="checkIn" data-id="{{ $patient->id }}" class="{{ $btnColor }}">Check In</a>
            </div>
        </div>
        <div class="body foldable-body">
            <div class="py">
                <h3><u>{{ $patient->name }}</u> ({{ substr($patient->gender_value, 0, 1) }})</h3>
                <p><b>Age: </b> {{ $patient->dob?->diffInYears() }} ({{ $patient->dob?->format('Y-m-d') }})</p>
                <p><b>Category:</b> {{ $patient->category->name }}</p>
                <p><b>Card Number:</b> {{ $patient->card_number }}</p>
                <p><b>Registration Date:</b> {{ $patient->created_at?->format('Y-m-d') }}</p>
            </div>
        </div>
    </div>

    {{-- Antenatal Profile --}}
    @if ($patient->category->name == 'Antenatal')
        <div class="card py px mb-1 foldable">
            <div class="header foldable-header">
                <div class="row between">
                    <p class="card-header">Antenatal Profile</p>
                    @if (isset($patient->antenatalProfiles[0]))
                        <a id="createAncVisit" href="#" data-id="{{ $patient->id }}"
                            class="{{ $btnColor }}">Check In</a>
                    @endif
                </div>
            </div>
            <div class="body foldable-body">
                <div class="py">
                    @if (!isset($patient->antenatalProfiles[0]))
                        <p class="text-danger">No antenatal profile found for this patient. <a
                                href="{{ route('records.patient.anc-profile', $patient) }}" id="createAncProfile"
                                data-id="{{ $patient->id }}">Create
                                Profile</a></p>
                    @else
                        <p><b>LMP Recorded: </b> {{ $patient->antenatalProfiles[0]?->lmp }}</p>
                        <p><b>Category:</b> {{ $patient->antenatalProfiles[0]?->card_type }}</p>
                        <p><b>Registration Date:</b> {{ $patient->antenatalProfiles[0]?->created_at->format('Y-m-d') }}</p>
                    @endif

                </div>
            </div>
        </div>
    @endif

    <div class="card py px mb-1 foldable">
        <div class="header foldable-header">
            <p class="card-header">Health Insurance</p>
        </div>
        <div class="body foldable-body">
            @if (isset($patient->insurance))
                <div class="py">
                    <p><b>HMO:</b> {{ $patient->insurance?->hmo_name }}</p>
                    <p><b>Company:</b> {{ $patient->insurance?->hmo_name }}</p>
                    <p><b>ID No:</b> {{ $patient->insurance?->hmo_id_no }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/records/patients.js'])
@endpush
