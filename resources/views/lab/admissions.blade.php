@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Admissions</h5>
            </div>
            <div class="card-body">
                <table class="table" id="table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Date</th>
                            <th>Ward</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($adm as $a)
                            <tr>
                                <td><a class="link" href="{{route('lab.admission-test', $a)}}">{{ $a->patient->name }}</a></td>
                                <td>{{ $a->created_at?->format('Y-m-d h:i A') }}</td>
                                <td>{{ $a->ward?->name }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
