@extends('layouts.app')

@section('content')
    <div class="card py px mt-2 foldable">
        <div class="header foldable-header">
            <div class="card-header">Antenatal Tests</div>
        </div>
        <div class="body foldable-body unfolded">
            <table class="my" id="anc-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th></th>
                        <th>Card Number</th>
                        <th>Date/Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $("#anc-table").DataTable({
            responsive: true,
            serverSide: true,
            ajax: "{{ route('api.lab.antenatal-tests') }}",
            columns: [{
                    data: 'patient.name'
                },
                {
                    data: 'profile.card_type'
                },
                {
                    data: 'patient.card_number'
                },
                {
                    data: (row, type, set) => new Date(row.created_at).toLocaleString("en-CA", {
                        year: 'numeric',
                        month: 'numeric',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric'
                    })
                },
                {
                    data: (row, type, set) => `<a href="{{ route('lab.test-anc', ':id') }}">Take Test</a>`
                        .replace(':id', row.id)
                },
            ],
        });
    </script>
@endpush
