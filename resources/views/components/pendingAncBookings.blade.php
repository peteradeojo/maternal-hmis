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

@push('scripts')
    <script>
        $(function() {
            $("#bookings").DataTable({
                serverSide: true,
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
                            `<a href='/med/anc-bookings/${row.id}'  class='btn bg-red-500 text-white px-2'>View</a>`
                    }
                ],
            });
        });
    </script>
@endpush
