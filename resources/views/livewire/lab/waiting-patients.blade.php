<div>
    <div class="card py px foldable">
        <div class="header foldable-header">
            <p class="card-header">Tests</p>
        </div>
        <div class="body foldable-body unfolded">
            <div class="py">
                <x-datatables id="tests-table">
                    <x-slot:thead>
                        <tr>
                            <th>Name</th>
                            <th>Card Number</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th></th>
                        </tr>
                    </x-slot:thead>
                </x-datatables>
            </div>
        </div>
    </div>
</div>


@pushOnce('scripts')
    <script>
        $("#tests-table").DataTable({
            serverSide: true,
            ordering: false,
            ajax: "{{ route('api.lab.tests') }}",
            columns: [{
                    data: 'patient.name'
                },
                {
                    data: 'patient.card_number'
                },
                {
                    data: ({
                        created_at
                    }) => parseDateFromSource(created_at),
                },
                {
                    data: 'patient.category.name'
                },
                {
                    data: 'patient.gender'
                },
                {
                    data: (row) => `<a href='{{ route('lab.view-tests', ':id') }}' class='link'>View Tests</a>`
                        .replace(':id', row.id)
                },
            ],
            responsive: true,
        });
    </script>
@endpushOnce
