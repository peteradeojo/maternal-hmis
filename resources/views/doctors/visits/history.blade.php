@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header header">History</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Card Number</th>
                    <th></th>
                    <th>Date</th>
                    <th>Category</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("table").DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ route('api.doctor.visits') }}",
                },
                columns: [{
                        data: (row) =>
                            `<a href='{{ route('doctor.visit', ':id') }}'>${row.patient.name}</a>`
                            .replace(':id', row.id)
                    },
                    {
                        data: 'patient.card_number'
                    },
                    {
                        data: 'visit.type'
                    },
                    {
                        data: (row) => new Date(row.created_at).toLocaleString()
                    },
                    {
                        data: 'patient.category.name'
                    },
                    {
                        data: (row) =>
                            `<a class='underline text-blue-600' href="/visits/${row.id}">View</a>`,
                    }
                ]
            });
        });
    </script>
@endpush
