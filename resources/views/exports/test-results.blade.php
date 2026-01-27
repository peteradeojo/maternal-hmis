@extends('layouts.pdf')

@section('content')
    <div class="max-w-full px-4">
        <p class="basic-header uppercase text-center">laboratory request form</p>
    </div>
    <div class="max-w-full p-4">
        <p><b>Patient name:</b> {{ $visit->patient->name }}</p>
        <p><b>Hospital No:</b> {{ $visit->patient->card_number }}</p>
        <p><b>Sex:</b> {{ $visit->patient->gender }}</p>
        <p><b>Date:</b> {{ $visit->created_at->format('Y-m-d') }}</p>
    </div>

    <div class="max-w-full p-4">
        @unless ($visit->diagnoses->isEmpty())
            <p><b>Provisional diagnosis:</b> {{ $visit->diagnoses }} </p>
        @endunless

        <p class="basic-header text-center uppercase pb-4">Test Results</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Reference Range</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($visit->valid_tests as $i => $test)
                    @if (count($test->results) > 1)
                    @else
                        <tr>
                            <td>{{ $test->results[0]->description }}</td>
                            <td>{{ $test->results[0]->unit }}</td>
                            <td>{{ $test->results[0]->reference_range }}</td>
                            <td>{{ $test->results[0]->result }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="max-w-full p-4">
        <p><b>Name of Doctor:</b> {{ $visit->doctor->name }}</p>
    </div>
@endsection
