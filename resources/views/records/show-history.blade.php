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
            </div>

            <div class="py-3">
                <p class="text-xl bold">Tests Taken</p>
                @php
                    $thisTotal = 0;
                @endphp

                <table class="table">
                    <thead>
                        <tr>
                            <th>Test</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visit->tests as $test)
                            <tr>
                                <td>{{ $test->name }}</td>
                                <td>{{ $test->describable?->amount ?? '0.00' }}</td>
                                @php
                                    $thisTotal += $test->describable?->amount;
                                @endphp
                            </tr>
                        @endforeach

                        @foreach ($visit->visit->tests as $test)
                            <tr>
                                @php
                                    $thisTotal += $test->describable?->amount;
                                @endphp
                                <td>{{ $test->name }}</td>
                                <td>{{ $test->describable?->amount ?? '0.00' }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="bold">Subtotal</td>
                            <td class="text-right bold">{{ number_format($thisTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="py-3">
                @php
                    $thisTotal = 0;
                @endphp
                <p class="text-xl bold">Radiology</p>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Procedure</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visit->imagings as $img)
                            <tr>
                                <td>{{ $img->name }}</td>
                                <td>{{ $img->describable?->amount ?? '0.00' }}</td>
                                @php
                                    $thisTotal += $img->describable?->amount;
                                @endphp
                            </tr>
                        @endforeach

                        @foreach ($visit->visit->radios as $test)
                            <tr>
                                @php
                                    $thisTotal += $test->describable?->amount;
                                @endphp
                                <td>{{ $test->name }}</td>
                                <td>{{ $test->describable?->amount ?? '0.00' }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td class="bold">Subtotal</td>
                            <td class="text-right bold">{{ number_format($thisTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
