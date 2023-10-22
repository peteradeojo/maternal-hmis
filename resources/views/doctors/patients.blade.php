@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="card-header">
            Patients
        </div>
        <div class="card-body py">
            <table id="patients-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Card Number</th>
                        <th>Category</th>
                        <th>Gender</th>
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
            $("#patients-table").DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ route('api.doctor.fetch-patients') }}"
                },
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'card_number'
                    },
                    {
                        data: 'category.name'
                    },
                    {
                        data: ({
                            gender
                        }) => gender == 1 ? "Male" : "Female"
                    },
                    {
                        data: ({id}) => `<a href='{{ route('doctor.patient', ':id') }}'>View</a>`.replace(':id', id)
                    }
                ],
            });
        });
    </script>
@endpushOnce
