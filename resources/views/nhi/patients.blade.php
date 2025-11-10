@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header header">Patients</div>
        <div class="body">
            <div class="pt-1"></div>
            <table id="table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Card Number</th>
                        <th>Phone Number</th>
                        <th>Registration</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("#table").DataTable({
                serverSide: true,
                responsive: true,
                ajax: "{{ route('api.nhi.patients') }}",
                columns: [{
                        data: ({
                                id,
                                name
                            }) =>
                            `<a data-id='${id}' class='link' href='{{route('records.patient', ':id')}}'>${name}</a>`.replace(':id', id)
                    },
                    {
                        data: 'card_number'
                    },
                    {
                        data: 'phone'
                    },
                    {
                        data: 'created_at',
                    }
                ]
            });
        });
    </script>
@endpush
