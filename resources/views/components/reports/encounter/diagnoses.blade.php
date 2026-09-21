<div class="py-2">
    <p class="text-lg font-semibold">Diagnoses</p>
    <table class="table">
        <thead>
            <tr>
                <th colspan="2">Diagnosis</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($visit->diagnoses as $diagnosis)
                <tr>
                    <td>{{ $diagnosis->diagnoses }}</td>
                    <td>{{ $diagnosis->consultant?->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
