@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header">Encounter Report</div>
        <button onclick="window.print()" class="btn btn-green btn-sm no-print">Print</button>
        <div class="body">
            <div class="col-6">
                <p class="underline"><b>Patient:</b> {{ $visit->patient->name }}</p>
                <p class="underline"><b>Date:</b> {{ $visit->created_at->format('Y-m-d h:i A') }}</p>
                <p><b>Type:</b> {{ $visit->type }}</p>
            </div>

            <div class="py-2"></div>
        </div>
        @include('doctors.components.history-report', ['visit' => $visit->visit])
    </div>
@endsection
