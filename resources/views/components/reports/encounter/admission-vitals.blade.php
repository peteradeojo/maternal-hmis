<div class="py-2">
    <p class="text-lg font-semibold">Admission Vitals Chart</p>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>BP</th>
                    <th>Weight</th>
                    <th>Temp</th>
                    <th>Pulse</th>
                    <th>Resp</th>
                    <th>SPO2</th>
                    <th>Fetal Heart Rate</th>
                    {{-- <th>Recorded By</th> --}}
                </tr>
            </thead>
            <tbody>
                @forelse($visit->svitals as $vital)
                    <tr>
                        <td>{{ $vital->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $vital->blood_pressure ?? '-' }}</td>
                        <td>{{ $vital->weight ?? '-' }}</td>
                        <td>{{ $vital->temperature ?? '-' }}</td>
                        <td>{{ $vital->pulse ?? '-' }}</td>
                        <td>{{ $vital->respiration ?? '-' }}</td>
                        <td>{{ $vital->spo2 ?? '-' }}</td>
                        <td>{{ $vital->fetal_heart_rate ?? '-' }}</td>
                        {{-- <td>{{ $vital->recorder?->name ?? 'Unknown' }}</td> --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">No vitals recorded during admission.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
