<div>
    <div class="card py px foldable">
        <div class="header foldable-header">
            <p class="card-header">Pending Tests</p>
        </div>
        <div class="body foldable-body unfolded">
            <div class="py">
                <table id="tests-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Card Number</th>
                            <th>Date</th>
                            <th>Gender</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentations as $doc)
                            <tr>
                                <td>{{ $doc->patient->name }}</td>
                                <td>{{ $doc->patient->card_number }}</td>
                                <td>{{ $doc->created_at }}</td>
                                <td>{{ $doc->patient->gender_value[0] }}</td>
                                <td>
                                    <a href="{{ route('lab.view-tests', $doc->patient_id) }}">View Tests</a>
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
            columns: [{
                orderable: false,
            }, {}, {}, {orderable: false,}, {orderable: false,}],
            order: [
                [2, 'desc']
            ],
        });
    </script>
@endpushOnce
