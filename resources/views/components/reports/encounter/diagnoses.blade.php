<div class="py-2">
    <p class="text-lg font-semibold">Diagnoses</p>
    <table class="table">
        <thead>
            <tr>
                <th>Diagnosis</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($visit->diagnoses as $diagnosis)
                <tr>
                    <td>{{ $diagnosis->diagnoses }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
