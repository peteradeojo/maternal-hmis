<div class="py-2">
    <p class="text-lg font-semibold">Complaints</p>
    <table class="table">
        <thead>
            <tr>
                <th>Presentations</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visit->histories->merge($visit->visit->histories) as $history)
                <tr>
                    <td>{{ $history->presentation }}</td>
                    <td>{{ $history->duration }}</td>
                </tr>
            @empty
                {{-- <tr>
                    <td colspan="2">No records</td>
                </tr> --}}
            @endforelse

            @foreach ($visit->complaints as $complaint)
                <tr>
                    <td>{{ $complaint->name }}</td>
                    <td>{{ $complaint->duration }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
