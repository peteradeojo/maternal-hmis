@extends('layouts.app')

@section('content')
    @php
        $grandTotal = 0;
    @endphp

    <div class="card">
        <div class="header">{{ $visit->patient->name }}</div>
        <div class="body p-3">
            <div>
                <p>Name: {{ $patient->name }} ({{ $patient->gender_value[0] }})</p>
                <p>Date: {{ $visit->created_at->format('Y-m-d h:i A') }}</p>
            </div>

            <livewire:records.bill-report :visit="$visit" />
        </div>
    </div>
@endsection
