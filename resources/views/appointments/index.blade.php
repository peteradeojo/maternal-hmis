@extends('layouts.app')
@section('title', 'Appointments')

@section('content')
    <div class="container">
        <div class="card">
            <p class="card-header">Appointments</p>

            <table id="table" class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('api.records.appointments') }}"
                },
                columns: [{
                        data: ({
                                patient,
                                id
                            }) =>
                            `<a href="{{ route('records.appointments.show', ':id') }}" class='link'>${patient.name}</a>`
                            .replace(':id', id),
                    },
                    {
                        data: 'patient.phone'
                    },
                    {
                        data: (row) => parseDateFromSource(row.appointment_date)
                    },
                    {
                        data: (row) => row.status == 1 ? `<div class='flex-center gap-x-2'>
                                <button class='btn bg-blue-400 text-white'>Check In</button>
                            </div>` : null
                    },
                ],
            })
        });
    </script>
@endpush
