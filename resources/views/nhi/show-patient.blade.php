@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header card-header">
            {{ $patient->name }}
        </div>
        <div class="body">
            <div class="pt-1"></div>
            <p><b>Card Number:</b> {{ $patient->card_number }}</p>
            <p><b>Date of Birth:</b> {{ $patient->dob?->format('Y-m-d') }}</p>
            <p><b>Category:</b> {{ $patient->category->name }}</p>
            <p><b>Registration Date:</b> {{ $patient->created_at->format('Y-m-d H:i A') }}</p>
        </div>
    </div>
@endsection
