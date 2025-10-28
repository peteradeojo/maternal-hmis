@extends('layouts.app')

@section('content')
    {{-- @livewire('nursing.anc-bookings') --}}

    <div class="card py px">
        <div class="header">
            <h1>New Antenatal Bookings</h1>
        </div>
        <div class="my-2">
            {{-- @include('components.pendingAncBookings') --}}
            <table id="bookings" class="table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Patient Name</th>
                        <th>Booking Date</th>
                        <th></th>
                        {{-- <th>Booking Time</th> --}}
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#bookings").DataTable({
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('api.nursing.anc-bookings') }}",
                },
                columns: [{
                        data: (row) => row.id
                    },
                    {
                        data: 'patient.name'
                    },
                    {
                        data: (row, type, set) => new Date(row.patient.created_at).toLocaleDateString()
                    },
                    {
                        data: (row, type, set) =>
                            `<a href='/anc-bookings/${row.id}'  class='btn bg-red-500 text-white px-2'>View</a>`
                    }
                ],
            });
        });
    </script>
@endpush
