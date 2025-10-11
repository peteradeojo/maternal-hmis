@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header">Scans</div>
        <div class="card-body">
            <table id="scans">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Scans</th>
                        <th>Date</th>
                        <th>Requested By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script>
        $(function() {
            $('#scans').DataTable({
                serverSide: true,
                responsive: true,
                ajax: '{!! route('api.rad.scans.data', ['patient_id' => @$patientId]) !!}',
                columns: [{
                        data: 'patient.name'
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: (row, type, name) => new Date(row.created_at).toLocaleString('en-GB')
                    },
                    {
                        data: 'requester.name'
                    },
                    {
                        data: (row) =>
                            `<a href="{{ route('rad.scan', ':id') }}" class="btn btn-sm bg-green-400">View</a>`
                            .replace(':id', row.id),
                    }
                ],
                responsive: true,
            });
        });
    </script>
@endPushOnce
