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
                <h3>Biodata</h3>
                <p><b>Name: </b>{{ $doc->patient->name }}</p>
                <p><b>Card Number: </b>{{ $doc->patient->card_number }}</p>
                <p><b>Age: </b>{{ $doc->patient->dob->diffInYears() }}</p>
                <p><b>Gender: </b>{{ $doc->patient->gender_value }}</p>
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
                <table class="table table-list">
                    <thead>
                        <tr>
                            <th>Prescription</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @foreach ($doc->treatments as $t)
                            <tr>
                                <td>{{ $t->name }}</td>
                                <td>{{ $t->dosage }}</td>
                                <td>{{ $t->frequency }}</td>
                                <td>{{ $t->duration }}</td>
                                <td>{{ $t->available !== null ? ($t->available ? 'Yes' : 'No') : 'Not Responded' }}</td>
                                <td>{{ $t->amount !== null ? $t->amount : 'Not Responded' }}</td>
                            </tr>
                            @php
                                $total += $t->available ? $t->amount ?? 0 : 0;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Total</td>
                            <td align="right">NGN</td>
                            <td align="right"><b>{{ number_format($total, 2) }}</b></td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
