<div>
    {{-- In work, do what you enjoy. --}}
    <div class="container card">
        <div class="card-header header">Profile</div>
        <div id="nav-tab" data-tablist="#tablist">
            <div id="tablist" class="py-1">
                <div class="tab">
                    <div>
                        {{ $visit->created_at?->format('Y-m-d h:i A') }}
                    </div>
                    <div class="grid grid-cols-3">
                        <p><b>Name</b>: {{ $visit->patient->name }}</p>
                        <p><b>Gender</b>: {{ $visit->patient->gender_value }}</p>
                        <p><b>Date of Birth</b>: {{ $visit->patient->dob?->format('Y-m-d') }}</p>
                        <p><b>Card Number</b>: {{ $visit->patient->card_number }}</p>
                        <p><b>Card Type</b>: {{ $visit->patient->category->name }}</p>
                        <p><b>Visit Type</b>: {{ $visit->type }}</p>
                        <p><b></b></p>
                        <p><b></b></p>
                        <p><b></b></p>
                        <p><b></b></p>
                    </div>
                </div>
                @if ($visit->type == 'Antenatal')
                    <div class="tab py-3 px-2">
                        <p class="text-3xl border-b border-gray-300">Antenatal Booking Details</p>
                        @isset($profile)
                            <livewire:antenatal-profile :profile="$profile" />
                        @else
                            <p>No data here yet.</p>
                        @endisset
                    </div>
                @else
                    <div class="tab">
                        <p class="bold mt-4 mb-1 text-xl header">Vitals</p>
                        @if ($visit->vitals)
                            <div class="grid grid-cols-2">
                                <p><b>Weight</b>: {{ $visit->vitals?->weight }} kg</p>
                                <p><b>Blood Pressure</b>: {{ $visit->vitals?->blood_pressure }} mmHg</p>
                                <p><b>Temperature</b>: {{ $visit->vitals?->temperature }} &deg;C</p>
                                <p><b>Pulse</b>: {{ $visit->vitals?->pulse }} bpm</p>
                                <p><b>Respiration</b>: {{ $visit->vitals?->respiration }} bpm</p>
                            </div>
                        @else
                            <p>No vitals have been recorded for this visit.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="py-1"></div>

    <div class="container card">
        <div id="actions-tab" data-tablist="#actions">
            <div class="flex justify-between items-center">
                @include('components.tabs', [
                    'options' =>
                        $visit->type == 'Antenatal'
                            ? ['Follow Up', 'First Visit', 'Medical Records']
                            : ['Medical Records'],
                ])

                <button class="btn btn-blue btn-sm modal-trigger" data-target="#previous-history-modal">View
                    History</button>
            </div>

            <div id="actions" class="py-1">
                @if ($visit->type == 'Antenatal')
                    <div class="tab">
                        @livewire('doctor.anc-visit', ['visit' => $visit])
                    </div>
                    <div class="tab">
                        <livewire:doctor.anc-booking :profile="$profile" :visit="$visit" />
                    </div>
                @endif

                <div class="tab">
                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#history-modal">Add History</button>

                    @if ($visit->examination)
                        <button class="btn btn-green btn-sm modal-trigger" data-target="#exams-modal">Edit
                            Examination</button>
                    @else
                        <button class="btn btn-blue btn-sm modal-trigger" data-target="#exams-modal">Add
                            Examination</button>
                    @endif

                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#notes-modal">Add Note</button>

                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#diagnosis-modal">Add
                        Diagnosis</button>
                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#tests-modal">Add
                        Investigation</button>
                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#prescriptions-modal">Add
                        Prescription</button>

                    <livewire:doctor.medical-records :visit="$visit" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal hide" id="prescriptions-modal">
        <div class="content p-3 bg-white">
            @livewire('doctor.add-presciption', ['visit' => $visit])
        </div>
    </div>

    <div class="modal hide" id="tests-modal">
        <div class="content p-3 bg-white">
            <p class="text-xl bold">Add Investigation</p>

            <div class="tablist" id="tests-tabs" data-tablist="#investigations">
                @include('components.tabs', ['options' => ['Lab', 'Radiology']])

                <div id="investigations" class="py-1">
                    <div class="tab">
                        <div class="form-group">
                            <label>Select Test</label>
                            <livewire:dynamic-product-search departmentId='5' @selected="addTest($event.detail.id)" />
                        </div>
                    </div>
                    <div class="tab">
                        <div class="form-group">
                            <label>Request Scan</label>
                            <livewire:dynamic-product-search departmentId='7' @selected="addScan($event.detail.id)" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal hide" id="history-modal">
        <div class="content p-3 bg-white">
            <p class="text-xl bold">Add History</p>
            <form wire:submit.prevent="addHistory" class="py-3">
                <div class="flex gap-x-3 py-3">
                    <div>
                        <input type="text" class="form-control" list="histories" placeholder="Presentation"
                            wire:model="historyForm.presentation" />
                        @error('historyForm.presentation')
                            <span class="error text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <input type="text" class="form-control" placeholder="Duration"
                            wire:model="historyForm.duration" />
                        @error('historyForm.duration')
                            <span class="error text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-blue" wire:click="addHistory">&plus; Add</button>
            </form>
        </div>
    </div>

    <div class="modal hide" id="exams-modal">
        <div class="content bg-white p-1 overflow-y-auto">
            <p class="text-xl bold">Examination</p>
            <form action="{{ route('doctor.examine', ['visit' => $visit->visit]) }}" id="exams-form" method="post">
                @csrf
                @include('components.examinations-form', ['exam' => $visit->examination])

                <button type="submit" class="btn btn-blue">Submit &triangleright;</button>
            </form>
        </div>
    </div>

    <datalist id="histories">
        @foreach ($histories as $history)
            <option value="{{ $history->presentation }}">{{ $history->presentation }}</option>
        @endforeach
    </datalist>

    <div class="modal hide" id="previous-history-modal">
        <div class="content max-w-full p-3 bg-white">
            <p class="text-xl bold">Previous Visits</p>

            <div class="py-4 grid grid-cols-2">
                <div>
                    @foreach ($visit->patient->visits as $previous_visit)
                        <button onClick="loadVisitReport({{ $previous_visit->id }})"
                            class="flex w-full justify-between py-2 px-3 bg-gray-200 hover:bg-gray-300">
                            <p class="text-xl bold">{{ $previous_visit->created_at->format('Y-m-d') }}</p>
                        </button>
                    @endforeach
                </div>
                <div class="border-2 border-l-0 overflow-y-auto p-3" id="previous-visit-report">
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        asyncForm("#exams-form", "{{ route('doctor.examine', ['visit' => $visit]) }}", async (e, res) => {
            const data = await res.json();
            console.log(data);

            $("#exams-modal").addClass("hide");
        });
    </script>
@endscript
