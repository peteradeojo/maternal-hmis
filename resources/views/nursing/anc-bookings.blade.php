@extends('layouts.app')

@section('content')
    @livewire('nursing.anc-bookings')

    <div class="card py px">
        <table id="bookings">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Patient Name</th>
                    <th>Booking Date</th>
                    <th>Booking Time</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#bookings").DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ route('api.nursing.anc-bookings') }}",
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'patient.name'
                    },
                    {
                        data: (row, type, set) => new Date(row.patient.created_at).toLocaleDateString("en-CA")
                    },
                    {
                        data: (row, type, set) => new Date(row.patient.created_at).toLocaleTimeString()
                    }
                ],
            });
        });
    </script>
@endpush
