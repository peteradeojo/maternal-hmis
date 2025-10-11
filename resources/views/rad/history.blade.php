@extends('layouts.app')

@section('content')
    <div class="card bg-white">
        <div class="header card-header">History</div>
        <div class="py-2 body">
            <table id="table" class="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Scans</th>
                        <th>Date</th>
                        <th>Requested By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $("#table").DataTable({
            serverSide: true,
            ajax: {
                url: "{{ route('api.rad.scans-history') }}",
                dataSrc: 'data'
            },
            columns: [{
                    data: 'patient.name'
                },
                {
                    data: 'name'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'requester.name'
                },
                {
                    data: (row) => `<a href="{{ route('rad.scan', ':id') }}" class='link'>View</a>`.replace(
                        ":id", row.id)
                },
            ],
            responsive: true,
        });
    </script>
@endpush
