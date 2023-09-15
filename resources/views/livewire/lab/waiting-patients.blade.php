<div wire:poll.5s>
    <div class="card py px foldable">
        <div class="header foldable-header">
            <p class="card-header">Pending Tests</p>
        </div>
        <div class="body foldable-body unfolded">
            <table id="tests-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Card Number</th>
                        <th>Gender</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentations as $doc)
                        <tr>
                            <td>{{ $doc->patient->name }}</td>
                            <td>{{ $doc->patient->card_number }}</td>
                            <td>{{ $doc->patient->gender_value[0] }}</td>
                            <td><a href="{{ route('lab.take-test', $doc->id) }}">Take Test</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@pushOnce('scripts')
    <script>
        $("#tests-table").DataTable();
    </script>
@endpushOnce
