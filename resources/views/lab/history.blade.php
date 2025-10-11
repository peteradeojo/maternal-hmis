@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card py px">
            <div class="header card-header">History</div>
            <div class="body my">
                <table id="history">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Card Number</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $("#history").DataTable({
            serverSide: true,
            ajax: {
                url: "{{ route('api.lab.history') }}",
                dataSrc: "data",
            },
            columns: [{
                    data: 'patient.name'
                },
                {
                    data: 'patient.card_number'
                },
                {
                    data: ({
                        created_at
                    }) => new Date(created_at).toLocaleDateString(),
                },
                {
                    data: (row, type, set) => {
                        return `<a href="{{ route('lab.report-test', ':id') }}">View</a>`.replace(':id', row.patient.id);
                    }
                }
            ],
        });
    </script>
@endpush
