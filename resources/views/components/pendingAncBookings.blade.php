<table id="bookings" class="table">
    <thead>
        <tr>
            <th>Patient Name</th>
            <th>Return Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

@push('scripts')
    <script>
        $(function() {
            $("#bookings").DataTable({
                responsive: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('api.doctor.anc-appointments') }}",
                },
                columns: [{
                        data: 'patient.name'
                    },
                    {
                        data: 'return_visit'
                    },
                    {
                        data: (row, type, set) =>
                            `<a href='{{ route('doctor.anc-profile', ':id') }}'  class='btn bg-red-500 text-white px-2'>View</a>`
                            .replace(':id', row.patient_id)
                    }
                ],
            });
        });
    </script>
@endpush
