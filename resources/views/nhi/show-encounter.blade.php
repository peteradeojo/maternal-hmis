<div>
    <x-patient-profile :patient="$visit->patient" />

    <p class="text-lg font-semibold">Encounter Details</p>
    <p>Date: {{ $visit->created_at }}</p>

    <div>
        <p class="py-2 font-semibold">Presentation: </p>
        <table class="table">
            @forelse ($visit->histories as $complaint)
                <tr>
                    <td>{{ $complaint->presentation }}</td>
                    <td>{{ $complaint->duration }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No complaints</td>
                </tr>
            @endforelse
        </table>
    </div>

    <div>
        <p class="py-2 font-semibold">Investigations</p>

        <table class="table">
            <tr>
                <th>Tests</th>
            </tr>
            @forelse ($visit->tests as $test)
                <tr>
                    <td>{{ $test->name }}</td>
                    <td>{{ $test->describable->amount }}</td>
                </tr>
            @empty
                <tr>
                    <td>No test</td>
                </tr>
            @endforelse

            <tr>
                <th>Imaging</th>
            </tr>
            @forelse ($visit->imagings as $img)
                <tr>
                    <td>{{ $img->name }}</td>
                    <td>{{ $img->describable->amount }}</td>
                </tr>
            @empty
                <tr>
                    <td>No scans requested</td>
                </tr>
            @endforelse
        </table>
    </div>

    <div>
        <p class="py-2 font-semibold">Diagnoses</p>
        @forelse ($visit->diagnoses as $dg)
            <div class="bg-green-100 p-1">
                <p>{{ $dg->diagnoses }}</p>
                <p><b>Consultant:</b> {{ $dg->made_by }}</p>
            </div>
        @empty
            <p>No diagnosis.</p>
        @endforelse
    </div>

    <div>
        <p class="py-2 font-semibold">Treatments</p>
        @forelse ($visit->treatments as $p)
            <div>
                {{ $p }}
            </div>
        @empty
            <p>No treatments</p>
        @endforelse
    </div>
</div>
