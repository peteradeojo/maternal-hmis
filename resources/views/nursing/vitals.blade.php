@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">Vitals</div>

        <div class="py-2">
            <table id="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Card Number</th>
                        <th>Category</th>
                        <th>Date/Time</th>
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
    <script defer>
        let dataTable = $("#table").DataTable({
            serverSide: true,
            ajax: {
                url: "{{route('api.nursing.vitals')}}",
                headers: {
                    "Accept": "application/json",
                }
            },
            language: {
                emptyTable: 'No results',
                searchPlaceholder: "Name, card number",
            },
            columns: [{
                    data: 'patient.name'
                },
                {
                    data: 'patient.card_number'
                },
                {
                    data: 'patient.category.name'
                },
                {
                    data: ({
                        created_at
                    }) => new Date(created_at).toLocaleString('en-CA'),
                },
                {
                    data: (row) =>
                        `<a href="{{ route('nurses.patient-vitals', ':id') }}" class="link">Take Vitals</a>`
                        .replace(':id', row.id),
                },
            ],
            order: [[3, 'desc']],
            responsive: true,
        });
    </script>
@endpush
