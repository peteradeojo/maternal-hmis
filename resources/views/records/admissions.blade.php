@extends('layouts.app')

@section('content')
    <div class="bg-white p-4">
        <table class="table" id="table">
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
                        <td><a href="{{route('doctor.visit', $a->visit)}}" class="link">{{ $a->patient->name }}</a></td>
                        <td>{{ $a->ward?->name }}</td>
                        <td>{{ $a->created_at }}</td>
                        <td>{{ $a->discharged_on ?? 'Not yet discharged' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $("#table").DataTable();
        });
    </script>
@endpush
