<table id="prescriptions">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Card Number</th>
            <th>Gender</th>
            <th>Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data ?? [] as $d)
            <tr>
                <td>{{ $d->patient->name }}</td>
                <td>{{ $d->patient->card_number }}</td>
                <td>{{ $d->patient->gender_value }}</td>
                <td>{{ $d->created_at->format('Y/m/d H:i A') }}</td>
                <td><a href="{{ route('phm.get-prescription', $d) }}">View</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
