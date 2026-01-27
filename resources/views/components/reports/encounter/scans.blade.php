<div class="py-2">
    <p class="text-lg font-semibold">Scans</p>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Report</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visit->radios->merge($visit->visit->radios) as $radio)
                <tr>
                    <td>{{ $radio->name }}</td>
                    <td>{{ $radio->getResults() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No records</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
