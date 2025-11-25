@extends('layouts.app')

@section('content')
    <div class="container bg-white p-4">
        <x-patient-profile :patient="$patient">
            <p><b>Age: </b> {{ $patient->age }}</p>
        </x-patient-profile>

        <div class="py-4">
            <p class="text-lg font-semibold">Antenatal Profile Details</p>

            {{-- <div class="grid grid-cols-3">
                <p><b>Booking date: </b> {{ $profile->created_at->format('Y-m-d') }}</p>
                <p><b>Gravida: </b> {{ $profile->gravida }}</p>
                <p><b>Parity: </b> {{ $profile->parity }}</p>
                <p><b>LMP: </b> {{ $profile->lmp?->format('Y-m-d') ?? 'N/A' }}</p>
                <p><b>Maturity: </b> {{ $profile->maturity() }}</p>
                <p><b>EDD: </b> {{ $profile->edd?->format('Y-m-d') ?? 'N/A' }}</p>
            </div> --}}

            <livewire:antenatal-profile :profile="$profile" />
        </div>

        <div class="py-4">
            <p class="text-lg font-semibold">Antenatal Visits</p>
            <x-anc-log :profile="$profile" :viewing="true" />
            {{-- @foreach ($profile->ancVisits as $visit)
            @endforeach --}}
        </div>
    </div>
@endsection
