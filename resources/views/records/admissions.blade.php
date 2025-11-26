@extends('layouts.app')

@section('content')
    <div class="bg-white p-4">
        <table class="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Ward</th>
                    <th>Date Admitted</th>
                    <th>Discharged</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admissions as $a)
                    <tr>
                        <td>{{ $a->patient->name }}</td>
                        <td>{{ $a->ward?->name }}</td>
                        <td>{{ $a->created_at }}</td>
                        <td>{{ $a->discharged_on ?? 'Not yet discharged' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
