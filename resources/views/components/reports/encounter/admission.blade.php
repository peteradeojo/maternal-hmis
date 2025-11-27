@if ($visit->admission)
    <div class="py-2 border-t mt-4">
        <h3 class="text-xl font-bold mb-2">Admission Report</h3>

        <div class="mb-4 p-3 bg-gray-50 rounded">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <strong>Ward:</strong> {{ $visit->admission->ward->name ?? 'N/A' }}
                </div>
                <div>
                    <strong>Date Admitted:</strong> {{ $visit->admission->created_at->format('d M Y h:i A') }}
                </div>
                <div>
                    <strong>Discharge Date:</strong>
                    {{ $visit->admission->discharged_on?->format('d M Y h:i A') ?? 'Not Discharged' }}
                </div>
                <div>
                    <strong>Indication:</strong> {{ $visit->admission->plan->indication ?? 'N/A' }}
                </div>
            </div>
            <div class="mt-2">
                <strong>Admission Plan:</strong>
                <p>{{ $visit->admission->plan->note ?? 'N/A' }}</p>
            </div>
            @if ($visit->admission->discharge_summary)
                <div class="mt-2 border-t pt-2">
                    <strong>Discharge Summary:</strong>
                    <p>{{ $visit->admission->discharge_summary }}</p>
                </div>
            @endif
        </div>

        <x-reports.encounter.notes-list :notes="$visit->admission->reviews" title="Review Notes" />

        @if ($visit->admission->operation_notes->isNotEmpty())
            <div class="py-2">
                <p class="text-lg font-semibold">Operation Notes</p>
                @foreach ($visit->admission->operation_notes as $opNote)
                    <div class="mb-3 p-3 border rounded">
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div><strong>Procedure:</strong> {{ $opNote->procedure }}</div>
                            <div><strong>Surgeon:</strong> {{ $opNote->surgeons }}</div>
                            <div><strong>Date:</strong> {{ $opNote->operation_date }}</div>
                            <div><strong>Indication:</strong> {{ $opNote->indication }}</div>
                        </div>
                        <div>
                            <strong>Findings:</strong>
                            <p>{{ $opNote->findings }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($visit->admission->delivery_note)
            <div class="py-2">
                <p class="text-lg font-semibold">Delivery Note</p>
                <div class="p-3 border rounded">
                    <p>{{ $visit->admission->delivery_note->note }}</p>
                    <small class="text-gray-600">
                        By: {{ $visit->admission->delivery_note->consultant?->name ?? 'Unknown' }}
                        at {{ $visit->admission->delivery_note->created_at->format('d M Y h:i A') }}
                    </small>
                </div>
            </div>
        @endif

        <x-reports.encounter.admission-vitals :visit="$visit->admission" />

        @if ($visit->admission->administrations->isNotEmpty())
            <div class="py-2">
                <p class="text-lg font-semibold">Treatment Administration Chart</p>
                @include('nursing.components.admission-treatments', ['admission' => $visit->admission])
            </div>
        @endif

        <x-reports.encounter.tests :visit="$visit->admission->plan" />
        <x-reports.encounter.scans :visit="$visit->admission" />
        <x-reports.encounter.prescriptions :visit="$visit->admission->plan" />
        <x-reports.encounter.notes-list :notes="$visit->admission->notes" title="All Notes" />
    </div>
@endif
