<div>
    <div class="card">
        <div class="header">
            <p>Patients</p>
        </div>
        <div class="body">
            <table class="table" id="patients">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Card Number</th>
                        <th>Category</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $i => $patient)
                        <tr>
                            <td>{{ $patient->get('name') }}</td>
                            <td>{{ $patient->get('card_number') }}</td>
                            <td>{{ $patient->get('category') }}</td>
                            <td>
                                <a href="{{ route('doctor.treat', $i+1) }}">View</a>
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
