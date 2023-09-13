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

@push('scripts')
    <script>
        $(function() {
            $("#bookings").DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ route('api.nursing.anc-bookings') }}",
                },
                columns: [{
                        data: @if(isset($url)) (row) => `<a href=:url>${row.id}</a>`.replace(":url", "{{ $url }}" + row.id)  @else 'id' @endif
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
