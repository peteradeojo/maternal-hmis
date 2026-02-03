<div>
    <div class="card py px">
        <div class="header card-header">
            <p>Patients</p>
        </div>
        <div class="body py">
            <x-datatables id="patients">
                <x-slot:thead>
                    <th>Name</th>
                    <th>Card Number</th>
                    <th>Card type</th>
                    <th>Visit Type</th>
                    <th>Date & Time</th>
                    <th></th>
                </x-slot:thead>
                <tbody></tbody>
            </x-datatables>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(() => {
            $("#patients").DataTable({
                processing: true,
                ordering: false,
                serverSide: true,
                ajax: "{{ route('api.doctor.consultations') }}",
                columns: [{
                        data: 'patient.name',
                        name: 'patient.name'
                    },
                    {
                        data: 'patient.card_number',
                        name: 'patient.card_number'
                    },
                    {
                        data: 'patient.category.name',
                        name: 'patient.category.name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: (row) => parseDateFromSource(row.created_at),
                        name: 'created_at'
                    },
                    {
                        data: (row) =>
                            `<a href="{{ route('doctor.treat', ':id') }}" class="link">Start Visit</a>`
                            .replace(':id', row.id)
                    }
                ],
                responsive: true,
            });
        })
    </script>
@endpush
