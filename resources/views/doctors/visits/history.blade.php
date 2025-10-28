@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header header">History</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Card Number</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Last Updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(async () => {
            await axios.get("{{route('sanctum.csrf-cookie')}}");

            $("table").DataTable({
                responsive: true,
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
                        data: (row) => new Date(row.updated_at).toLocaleString()
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
