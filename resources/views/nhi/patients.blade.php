@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header header">Patients</div>
        <div class="body">
            <div class="pt-1"></div>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Patient</th>
                        <th>Card Number</th>
                        <th>HMO</th>
                        <th>Phone Number</th>
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
            $("table").DataTable({
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('api.nhi.patients') }}"
                },
                columns: [{
                        data: (row) => `<a href='{{ route('nhi.show-patient', ':id') }}'>${row.id}</a>`
                            .replace(':id', row.id)
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'card_number'
                    },
                    {
                        data: 'insurance.hmo_name'
                    },
                    {
                        data: 'phone'
                    },
                ]
            });
        });
    </script>
@endpush
