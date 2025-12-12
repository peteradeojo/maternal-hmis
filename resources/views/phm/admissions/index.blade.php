@extends('layouts.app')

@section('content')
    <div class="card p-4">
        <p class="basic-header">Admissions</p>

        <table class="table" id="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Category</th>
                    <th>Ward</th>
                    <th>Date admitted</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admissions as $adm)
                    <tr>
                        <td><a href="" class="link">{{ $adm->patient->name }} ({{ $adm->patient->card_number }})</a>
                        </td>
                        <td>{{ $adm->patient->category->name }}</td>
                        <td>{{ $adm->ward->name }}</td>
                        <td>{{ $adm->created_at?->format('Y-m-d h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("#table").DataTable();
        });
    </script>
@endpush
