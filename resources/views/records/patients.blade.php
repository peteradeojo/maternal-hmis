@extends('layouts.app')
@section('title', 'Patients')

@section('content')
    <x-back-link to="{{ route('dashboard') }}" />
    <div class="container">
        <div class="card py px mb-1">
            <h2>Register Patient</h2>

            <div class="py flex gap-x-3">
                <a href="{{ route('records.patients.new') }}" class="btn bg-green-500">General Patient</a>
                <a href="{{ route('records.patients.new') }}?mode=anc" class="btn bg-red-500  text-white">Antenatal
                    Patient</a>
            </div>
        </div>

        <div class="card py px">
            <h2 class="my">Patients</h2>

            {{-- <livewire:records.patient-search /> --}}

            <table class="table" id="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Card Number</th>
                        <th>Category</th>
                        <th>Gender</th>
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
            $("#table").DataTable({
                serverSide: true,
                ajax: "{{ route('api.records.patients') }}",
                ordering: false,
                columns: [{
                        data: (row) =>
                            `<a href='{{ route('records.patient', ':row') }}' class='link'>${row.name}</a>`
                            .replace(':row', row.id),
                        name: 'id'
                    },
                    {
                        data: 'card_number',
                        name: 'card_number',
                    },
                    {
                        data: 'category.name',
                        name: 'category.name',
                    },
                    {
                        data: 'gender_value',
                        name: 'gender',
                    },
                    {
                        data: 'phone'
                    },
                ],
            });
        })
    </script>
@endpush
