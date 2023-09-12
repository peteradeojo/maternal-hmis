@extends('layouts.app')

@section('content')
    @livewire('nursing.anc-bookings', ['user' => auth()->user()])
    <div class="card py px">
        <div class="header">
            <p class="card-header">New Antenatal Bookings</p>
        </div>
        <div class="body">
            <table id="bookings" class="my">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>LMP</th>
                        <th>EDD</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
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
                    data: ({
                        lmp
                    }) => new Date(lmp).toLocaleDateString("en-CA", {timeZone: "Africa/Lagos"})
                },
                {
                    data: ({
                        edd
                    }) => new Date(edd).toLocaleDateString("en-CA", {timeZone: "Africa/Lagos"})
                },
            ],
        });
    </script>
@endpush
