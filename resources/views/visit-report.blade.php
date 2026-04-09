<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        div {
            padding: 8px;
        }

        div.container {
            padding: 8px 24px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table td,
        table th {
            border: 1px solid #ccc;
            border-collapse: collapse;
            padding: 4px 2px;
            text-align: left;
        }

        ul {
            padding: 0;
            list-style-position: inside;
        }

        li p {
            display: inline
        }

        .py-2 {

            padding: .7em 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Maternal-Child Specialists' Clinics</h1>
        <h3>Patient Encounter Report</h3>
    </div>

    <div class="container">
        <h3>Patient Information</h3>
        <table>
            <tr>
                <th>Name</th>
                <td>{{ $visit->patient->name }}</td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ $visit->patient->gender }}</td>
            </tr>
            <tr>
                <th>Date of birth</th>
                <td>{{ $visit->patient->dob?->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <th>Patient no.</th>
                <td>{{ $visit->patient->card_number }}</td>
            </tr>
        </table>
    </div>

    <div class="container">
        <h3 class="py-2">Encounter</h3>
        <p><b>Patient check-in:</b> {{ $visit->created_at->format('Y-m-d H:i A') }}</p>

        <h3>Vitals</h3>
        <table>
            {{-- @dump($visit->vitals) --}}
            <tr>
                <th>Weight (kg)</th>
                <td>{{ $visit->vitals->weight }}</td>
            </tr>
            <tr>
                <th>Temperature (&deg;C)</th>
                <td>{{ $visit->vitals->temperature }}</td>
            </tr>
            <tr>
                <th>Blood presure (mmHg)</th>
                <td>{{ $visit->vitals->blood_pressure }}</td>
            </tr>
            <tr>
                <th>Respiration (c/m)</th>
                <td>{{ $visit->vitals->respiration }}</td>
            </tr>
            <tr>
                <th>Pulse (bpm)</th>
                <td>{{ $visit->vitals->pulse }}</td>
            </tr>
            <tr>
                <th>SPO2</th>
                <td>{{ $visit->vitals->spo2 }}</td>
            </tr>
        </table>
    </div>

    <div class="container">
        <h3 class="py-2">Presentation</h3>
        <ul>
            @foreach ($visit->histories as $cc)
                <li>{{ $cc->presentation }} [{{ $cc->duration }}]</li>
            @endforeach
        </ul>
    </div>

    <div class="container">
        <h3 class="py-2">Examinations</h3>

        <p><strong>General Examination</strong></p>
        <p>{{ $visit->examination?->general }}</p>

        <p><strong>Others</strong></p>
        @forelse (array_filter($visit->examination?->specifics ?? []) as $sp => $val)
            <p><b>{{ $sp }}</b>: {{ $val }}</p>
        @empty
            <p>No other examinations recorded.</p>
        @endforelse

        <p class="py-2"><strong>Consultant's notes</strong></p>

        <ul>
            @forelse ($visit->notes as $note)
                <li>
                    <span>@nl2br($note->note)</span>
                </li>
            @empty
                <li>No notes recorded.</li>
            @endforelse
        </ul>

        <p class="py-2"><strong>Diagnoses</strong></p>

        <ul>
            @forelse ($visit->diagnoses as $d)
                <li>
                    <span>{{ $d->diagnoses }}</span>
                </li>
            @empty
            @endforelse
        </ul>
    </div>

    <div class="container">
        <h3 class="py-2">Laboratory Tests</h3>

        <table>
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Result</th>
                    {{-- <th>Status</th> --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($visit->tests as $test)
                    <tr>
                        <td>{{ $test->name }}</td>
                        <td>
                            @if ($test->results)
                                @if (is_array($test->results) || is_object($test->results))
                                    @foreach ((array) $test->results as $result)
                                        @if (is_object($result) && isset($result->description) && isset($result->result))
                                            <b>{{ $result->description }}</b>: {{ $result->result }} <br>
                                        @elseif(is_string($result))
                                            {{ $result }} <br>
                                        @endif
                                    @endforeach
                                @else
                                    {{ $test->results }}
                                @endif
                            @else
                                No result
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No laboratory tests requested.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="container">
        <h3 class="py-2">Radiology & Imaging</h3>
        <ul>
            @forelse ($visit->radios as $rad)
                <li>
                    <b>{{ $rad->name }}</b> ({{ Status::from($rad->status)->name }})
                    <br />
                    <span>{{ $rad->getResults() }}</span>
                </li>
            @empty
                <li>No radiology requests.</li>
            @endforelse
        </ul>
    </div>

    <div class="container">
        <h3 class="py-2">Prescriptions & Treatments</h3>
        <ul>
            @forelse ($visit->prescription?->lines ?? [] as $treatment)
                <li>{{ $treatment }}</li>
            @empty
                <li>No prescriptions recorded.</li>
            @endforelse
        </ul>
    </div>

    @if ($visit->admission)
        <div class="container">
            <h3 class="py-2">Admission Details</h3>

            {{-- {{ $visit->admission }} --}}

            <table>
                <tr>
                    <th>Date admitted</th>
                    <td>{{ $visit->admission->created_at->format('Y-m-d h:i a') }}</td>
                </tr>
                <tr>
                    <th>Date discharged</th>
                    <td>{{ $visit->admission->discharged_on?->format('Y-m-d h:i a') }}</td>
                </tr>
                <tr>
                    <th>Indication</th>
                    <td>{{ $visit->admission->plan?->indication }}</td>
                </tr>
                <tr>
                    <th>Ward</th>
                    <td>{{ $visit->admission->ward?->name }}</td>
                </tr>
                <tr>
                    <th>Discharge Summary</th>
                    <td>{{ $visit->admission->discharge_summary }}</td>
                </tr>
            </table>
        </div>

        <div class="container">
            <h3 class="py-2">Admission Reviews</h3>

            <ul>
                @forelse ($visit->admission->notes as $note)
                    <li class="py-2">
                        <span>@nl2br($note->note)</span><br />
                        <span style="font-size: .7em">{{ $note->created_at->format('Y-m-d h:i a') }} -
                            {{ $note->recorder?->name ?? $note->consultant->name }}</span>
                    </li>
                @empty
                    <li>No notes.</li>
                @endforelse
            </ul>
        </div>

        <div class="container">
            <h3 class="py-2">Surgical Procedures</h3>

            @forelse ($visit->admission->operation_notes as $opNote)
                <table class="py-2">
                    <tr>
                        <th>Date</th>
                        <td>{{ $opNote->created_at->format('Y-m-d h:i a') }}</td>
                    </tr>
                    <tr>
                        <th>Indication</th>
                        <td>{{ $opNote->indication }}</td>
                    </tr>
                    <tr>
                        <th>Procedure</th>
                        <td>{{ $opNote->procedure_name }}</td>
                    </tr>
                    <tr>
                        <th>Consultant</th>
                        <td>{{ $opNote->consultant }}</td>
                    </tr>
                    <tr>
                        <th>Surgeon(s)</th>
                        <td>{{ $opNote->surgeons }}</td>
                    </tr>
                    <tr>
                        <th>Assistant(s)</th>
                        <td>{{ $opNote->assistants }}</td>
                    </tr>
                    <tr>
                        <th>Scrub nurse</th>
                        <td>{{ $opNote->scrub_nurse }}</td>
                    </tr>
                    <tr>
                        <th>Circulating nurse</th>
                        <td>{{ $opNote->circulating_nurse }}</td>
                    </tr>
                    <tr>
                        <th>Anaesthesist(s)</th>
                        <td>{{ $opNote->anaesthesists }}</td>
                    </tr>
                    <tr>
                        <th>Type of Anaesthesia</th>
                        <td>{{ $opNote->anaesthesia_type }}</td>
                    </tr>
                    <tr>
                        <th>Incision</th>
                        <td>{{ $opNote->incision }}</td>
                    </tr>
                    <tr>
                        <th>Findings</th>
                        <td>{{ $opNote->findings }}</td>
                    </tr>
                    <tr>
                        <th>Procedure details</th>
                        <td>{{ $opNote->procedure }}</td>
                    </tr>
                </table>
            @empty
                <p>No operations performed.</p>
            @endforelse
        </div>
    @endif
</body>

</html>
