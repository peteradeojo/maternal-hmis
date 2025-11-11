<div>
    <div class="card py px foldable">
        <div class="header foldable-header">
            <p class="card-header">Pending Tests</p>
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
                        @foreach ($visits as $doc)
                            <tr>
                                <td>{{ $doc->patient->name }}</td>
                                <td>{{ $doc->patient->card_number }}</td>
                                <td>{{ $doc->created_at }}</td>
                                <td>{{ $doc->patient->category->name }}</td>
                                <td class="text-center">{{ $doc->patient->gender[0] }}</td>
                                <td>
                                    <a href="{{ route('lab.view-tests', $doc->id) }}">View Tests</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@pushOnce('scripts')
    <script>
        $("#tests-table").DataTable({
            order: [
                [2, 'desc']
            ],
            responsive: true,
        });
    </script>
@endpushOnce
