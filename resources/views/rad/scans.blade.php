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
                ajax: '{!! route('api.rad.scans.data') !!}',
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'scans',
                    },
                    {
                        data: (row, type, name) => Date(row.created_at).slice(0, 15)
                    },
                    {
                        data: 'requester.name'
                    },
                    {
                        data: (row) => `<a href="{{ route('rad.scan', ':id') }}" class="btn btn-sm btn-primary">View</a>`.replace(':id', row.id),
                    }
                ]
            });
        });
    </script>
@endPushOnce
