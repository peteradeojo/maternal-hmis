<div class="py-2">
    <p class="text-lg font-semibold">Vitals</p>
    <table class="table">
        <thead>
            <tr>
                <th>Blood Pressure</th>
                <th>Weight</th>
                <th>Temperature</th>
                <th>Pulse</th>
                <th>Respiration</th>
                <th>SPO2</th>
                @if ($visit->vitals?->fetal_heart_rate)
                    <th>Fetal Heart Rate</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($visit->vitals)
                <tr>
                    <td>{{ $visit->vitals->blood_pressure }}</td>
                    <td>{{ $visit->vitals->weight }}</td>
                    <td>{{ $visit->vitals->temperature }}</td>
                    <td>{{ $visit->vitals->pulse }}</td>
                    <td>{{ $visit->vitals->respiration }}</td>
                    <td>{{ $visit->vitals->spo2 }}</td>
                    @if ($visit->vitals->fetal_heart_rate)
                        <td>{{ $visit->vitals->fetal_heart_rate }}</td>
                    @endif
                </tr>
                <tr>
                    <td>
                        <p class="basic-header">Extra</p>
                        @foreach ($visit->vitals->extra as $k => $value)
                            <p><b>{{ ucfirst(unslug($k)) }}</b>: {{ is_bool($value) ? ($value == true ? 'Yes' : 'No') : $value }}</p>
                        @endforeach
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="7">No vitals recorded.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
