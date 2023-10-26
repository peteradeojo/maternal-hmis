@extends('layouts.app')
@section('title', 'Prescription:: ' . $doc->patient->name)

@section('content')
    <div class="card py px foldable folded">
        <div class="header foldable-header">
            <div class="card-header">
                {{ $doc->patient->name }}
            </div>
        </div>
        <div class="body foldable-body unfolded">
            <div class="py">
                <p><b>Name: </b>{{ $doc->patient->name }}</p>
                <p><b>Gender: </b>{{ $doc->patient->gender_value }}</p>
                <p><b>Date: </b> {{ $doc->created_at->format('Y-m-d H:i A') }}</p>
            </div>
        </div>
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
                @livewire('dis.prescription', ['doc' => $doc])
            </div>
        </div>
    </div>
@endsection
