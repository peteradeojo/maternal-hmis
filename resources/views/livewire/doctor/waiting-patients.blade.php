<div>
    <div class="card py px">
        <div class="header card-header">
            <p>Patients</p>
        </div>
        <div class="body py" wire:poll.visible.15s>
            <table class="table" id="patients">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Card Number</th>
                        <th>Category</th>
                        <th>Visit Type</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visits as $v)
                        <tr>
                            <td>{{ $v->patient->name }}</td>
                            <td>{{ $v->patient->card_number }}</td>
                            <td>{{ $v->patient->category->name }}</td>
                            <td>{{ $v->getVisitType() }}</td>
                            <td>
                                <a href="{{ route('doctor.treat', $v) }}">Start Visit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        new DataTable('#patients');
    </script>
@endpush
