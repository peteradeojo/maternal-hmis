<div class="py-2">
    <p class="text-lg font-semibold">Prescriptions</p>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($visit->treatments->merge($visit->visit?->treatments ?? []) as $treatment)
                <tr>
                    <td>{{ $treatment }}</td>
                </tr>
            @endforeach

            @foreach ($visit->prescription?->lines ?? [] as $treatment)
                @continue($treatment->status == Status::blocked)
                <tr>
                    <td>{{ $treatment }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
