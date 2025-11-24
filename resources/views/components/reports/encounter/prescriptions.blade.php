<div class="py-2">
    <p class="text-lg font-semibold">Prescriptions</p>

    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Dosage</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visit->treatments->merge($visit->visit->treatments) as $treatment)
                <tr>
                    <td>{{ $treatment->name }}</td>
                    <td>{{ $treatment->dosage }}</td>
                    <td>{{ $treatment->duration }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No records</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
