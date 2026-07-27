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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script>
        $(() => {
            $("#table").DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('api.lab.admissions') }}",
                },
                columns: [{
                        data: 'patient.name'
                    },
                    {
                        data: (row) => parseDateFromSource(row.created_at)
                    },
                    {
                        data: 'ward.name'
                    },
                    {
                        data: (row) =>
                            `<a href="{{ route('lab.admission-test', ':id') }}" class='btn ${row.has_pending_tests ? 'bg-green-500' : 'bg-blue-500 text-white'}'>${row.has_pending_tests ? 'View Tests' : 'Order A Test'}</a>`
                            .replace(':id', row
                                .id),
                    }
                ],
            });
        });
    </script>
@endPushOnce
