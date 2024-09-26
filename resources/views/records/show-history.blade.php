@extends('layouts.app')

@section('content')
    @php
        $grandTotal = 0;
    @endphp

    <div class="card">
        <div class="header">{{ $patient->name }}</div>
        <div class="body p-3">
            <div>
                <p>Name: {{ $patient->name }} ({{ $patient->gender_value[0] }})</p>
                <p>Date: {{ $visit->created_at->format('Y-m-d h:i A') }}</p>
            </div>

            @if ($visit->type == 'Antenatal')
                <p class="text-xl mt-3 bold">Note</p>
                <p>Next visit: {{ $visit->return_visit }}</p>
            @endif

            <livewire:records.bill-report :visit="$visit" />


            <a class="btn btn-red" href="{{ route('records.force-check-out', ['visit' => $visit]) }}?force">Check Out</a>
        </div>
    </div>
@endsection
