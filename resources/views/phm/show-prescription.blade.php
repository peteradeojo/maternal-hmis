@extends('layouts.app')

@section('content')
    <div class="card p-2">
        <x-patient-profile :patient="$prescription->patient" />
    </div>
    <div class="py"></div>
    <div class="card py px">
        <div class="header">
            <div class="card-header">
                Prescriptions
            </div>
        </div>
        <div class="card-body">
            <div class="py">
                @livewire('phm.prescription', ['doc' => $prescription])
            </div>
        </div>
    </div>
@endsection
