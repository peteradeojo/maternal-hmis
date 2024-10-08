<div>
    <div class="card py px">
        <div class="header card-header">
            <p>Patients</p>
        </div>
        <div class="body py">
            <table class="table" id="patients">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Card Number</th>
                        <th>Category</th>
                        <th>Visit Type</th>
                        <th>Date & Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visits as $v)
                        <tr>
                            <td>{{ $v->patient->name }}</td>
                            <td>{{ $v->patient->card_number }}</td>
                            <td>{{ $v->patient->category->name }}</td>
                            <td>{{ $v->readable_visit_type }}</td>
                            <td>{{ $v->created_at }}</td>
                            <td>
                                @if ($v->waiting_for_doctor)
                                    <a href="{{ route('doctor.treat', $v) }}">Review Last
                                        Documentation</a>
                                @else
                                    <a class="text-blue-600 underline" href="{{ route('doctor.treat', $v) }}">Start
                                        Visit</a>
                                @endif
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
        new DataTable('#patients', {
            // ordering: false,
            order: [
                [4, 'desc']
            ]
        });
    </script>
@endpush
