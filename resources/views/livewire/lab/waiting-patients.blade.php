<div>
    <div class="card py px foldable">
        <div class="header foldable-header">
            <p class="card-header">Tests</p>
        </div>
        <div class="body foldable-body unfolded">
            <div class="py">
                <table id="tests-table" class="table rounded-md">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Card Number</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Gender</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@pushOnce('scripts')
    <script>
        $("#tests-table").DataTable({
            serverSide: true,
            order: [
                [2, 'desc']
            ],
            ajax: "{{ route('api.lab.tests') }}",
            columns: [{
                    data: 'patient.name'
                },
                {
                    data: 'patient.card_number'
                },
                {
                    data: ({created_at}) => new Date(created_at).toLocaleString('en-US', {
                        timeZone: 'Africa/Lagos'
                    }),
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
