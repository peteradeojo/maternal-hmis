
    <p class="text-2xl bold">Medical Report</p>
    <div class="col-6">
        <p>Vitals</p>
        @include('doctors.components.vitals', ['visit' => $visit])
    </div>

    <div class="pt-4">
        <p class="text-lg bold">Patient History</p>
        @empty($visit->histories->toArray())
            <p>No records</p>
        @else
            @foreach ($visit->histories as $history)
                @dump($history)
            @endforeach
        @endempty
    </div>

    <div class="pt-4">
        <p class="text-lg bold">Diagnoses</p>
        @empty($visit->diagnoses->toArray())
            <p>No records</p>
        @else
            @foreach ($visit->diagnoses as $history)
                @dump($history)
            @endforeach
        @endempty
    </div>

    <div class="pt-4">
        <p class="text-lg bold">Tests</p>
        @empty($visit->tests->toArray())
            <p>No records</p>
        @else
            @include('doctors.components.test-results', ['tests' => $visit->tests, 'cancellable' => false])
        @endempty
    </div>

    <div class="pt-4">
        <p class="text-lg bold">Scans</p>
        @empty($visit->radios->toArray())
            <p>No records</p>
        @else
            <div class="p-2 bg-gray-100">
                @foreach ($visit->radios as $radio)
                    <div>
                        <p class="text-md bold">{{ $radio->name }}</p>
                        @unless ($radio->comment || $radio->path)
                            <p class="text-sm">No result</p>
                        @else
                            <p class="text-sm">{{ $radio->comment }}</p>
                        @endunless
                    </div>
                @endforeach
            </div>
        @endempty
    </div>

    <div class="pt-4">
        <p class="text-lg bold">Treatments & Prescriptions</p>
        @empty($visit->treatments->toArray())
            <p>No records</p>
        @else
            <table class="table">
                <thead>
                    <tr>Item</tr>
                    <tr>Dosage</tr>
                    <tr>Duration</tr>
                </thead>
                <tbody>
                    @foreach ($visit->treatments as $treatment)
                        <tr>
                            <td>{{ $treatment->name }}</td>
                            <td>{{ $treatment->dosage }}</td>
                            <td>{{ $treatment->duration }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endempty
    </div>

