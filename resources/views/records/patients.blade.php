@extends('layouts.app')
@section('title', 'Patients')

@section('content')
    <div class="container">
        <div class="card py px mb-1">
            <h2>Register Patient</h2>

            <div class="py flex gap-x-3">
                <a href="{{ route('records.patients.new') }}" class="btn bg-green-500">General Patient</a>
                <a href="{{ route('records.patients.new') }}?mode=anc" class="btn bg-red-500  text-white">Antenatal Patient</a>
            </div>
        </div>

        <div class="card py px">
            <h2 class="my">Patients</h2>
            <table id="patients">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Card Number</th>
                        <th>Gender</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- @vite(['resources/js/records/patients.js']) --}}

    <script>
        $("#patients").DataTable({
            serverSide: true,
            ajax: {
                url: "{{ route('api.records.patients') }}",
                dataSrc: "data",
                // type: 'POST',
                // headers: {
                //     "X-CSRF-TOKEN": "{{ csrf_token() }}"
                // }
            },
            columns: [{
                    data: 'name'
                },
                {
                    data: 'category.name'
                },
                {
                    data: 'card_number'
                },
                {
                    data: ({
                        gender_value
                    }, type, set) => {
                        return gender_value[0];
                    }
                },
                {
                    data: function(row, type, set) {
                        return `<a href="{{ route('records.patient', ':id') }}">View</a>`.replace(':id', row
                            .id);
                    }
                }
            ],
        });
    </script>
@endpush
