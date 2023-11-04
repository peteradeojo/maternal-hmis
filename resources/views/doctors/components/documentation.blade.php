<div>
    <hr>
    <p class="pt-1 px">Time: {{ $doc->created_at->format('Y-m-d h:i A') }}</p>
    <div class="row start">
        <div class="col-4 px py">
            <p><u>Complaints:</u></p>
            <p>{{ join(', ', $doc->complaints->map(fn($c) => "{$c->name}" . ($c->duration ? " for ({$c->duration})" : null))->toArray()) }}</p>
        </div>
        <div class="col-4 px py">
            <p><u>History of Complaint:</u></p>
            <p>{{ $doc->complaints_history ?? 'Nil' }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 px py">
            <p><u><b>Examinations</b></u></p>
            <div class="pt-1"></div>
            <p>General Physical Examination: {{ $doc->exams->first()?->general ?? 'Nil' }}</p>
            <div class="pt-1"></div>
            <p>Specific Examinations:</p>
            @forelse ($doc->exams->first()?->specifics ?? [] as $e => $v)
                <p>{{ ucfirst(str_replace('_', ' ', $e)) }}: {{ $v ?? 'Nil' }}</p>
            @empty
                Nil
            @endforelse
        </div>
    </div>
    <div class="row start">
        <div class="col-6 py px">
            <p><b><u>Tests:</u></b></p>
            <div class="pt-1"></div>
            <table class="table bordered">
                <thead>
                    <tr>
                        <th>Test</th>
                        <th>Result (unit)</th>
                        <th>Reference Range</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($doc->tests as $test)
                        <tr>
                            <td colspan="4" align="center" class="py">{{ strtoupper($test->name) }}</td>
                        </tr>
                        @forelse ($test->results ?? [] as $result)
                            <tr>
                                <td>{{ $result->description }}</td>
                                <td>{{ $result->result }} {{ $result->unit }}</td>
                                <td>{{ $result->reference_range }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" align="center">No results submitted</td>
                            </tr>
                        @endforelse
                    @empty
                        <tr>
                            <td colspan="4" align="center">No Test</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="col-6 py px">
            <p><b><u>Radiology:</u></b></p>
        </div>
    </div>
    <div class="row start">
        <div class="col-6 px py">
            <p><b><u>Diagnosis:</u></b> {{ $doc->diagnoses->count() > 1 ? join(',', $doc->diagnoses?->map(fn ($d) => $d->diagnoses)->toArray()) : "Nil" }}</p>
        </div>
        <div class="col-6 px py">
            <p><b><u>Prescriptions:</u></b></p>
            <div class="pt-1"></div>
            <ul>
                @forelse ($doc->treatments as $p)
                    <li>{{ $p->name }} {{ $p->route }} {{ $p->dosage }} {{ $p->frequency }} {{ $p->duration }}</li>
                @empty
                    <li>No Prescription</li>
                @endforelse
            </ul>
        </div>
    </div>
    {{-- @include('doctors.components.vitals') --}}
</div>
<div class="pt-3"></div>
