<div>
    {{-- In work, do what you enjoy. --}}
    <div class="container card">
        <div class="card-header header">Profile</div>
        <div id="nav-tab" data-tablist="#tablist">
            {{-- @include('components.tabs', ['options' => ['Profile', 'Vitals']]) --}}
            <div id="tablist" class="py-1">
                <div class="tab">
                    <div class="grid grid-cols-3">
                        <p><b>Name</b>: {{ $visit->patient->name }}</p>
                        <p><b>Gender</b>: {{ $visit->patient->gender_value }}</p>
                        <p><b>Date of Birth</b>: {{ $visit->patient->dob?->format('Y-m-d') }}</p>
                        <p><b>Card Number</b>: {{ $visit->patient->card_number }}</p>
                        <p><b>Card Type</b>: {{ $visit->patient->category->name }}</p>
                        <p><b>Visit Type</b>: {{ $visit->readable_visit_type }}</p>
                        <p><b></b></p>
                        <p><b></b></p>
                        <p><b></b></p>
                        <p><b></b></p>
                    </div>
                </div>
                @if ($visit->readable_visit_type == 'Antenatal')
                    <div class="tab my-5">
                        <p class="text-3xl border-b border-gray-300 pb-2 mb-2">Antenatal Booking Details</p>
                        @isset($visit->visit->profile)
                            @include('components.anc-profile', [
                                'ancProfile' => $visit->patient->ancProfile,
                            ])
                        @else
                            <p>No data here yet.</p>
                        @endisset
                    </div>
                @else
                    <div class="tab">
                        <p class="bold mt-4 mb-1 text-xl header">Vitals</p>
                        @isset($visit->svitals)
                            <div class="grid grid-cols-2">
                                <p><b>Weight</b>: {{ $visit->svitals->weight }} kg</p>
                                <p><b>Blood Pressure</b>: {{ $visit->svitals->blood_pressure }} mmHg</p>
                                <p><b>Temperature</b>: {{ $visit->svitals->temperature }} &deg;C</p>
                                <p><b>Pulse</b>: {{ $visit->svitals->pulse }} bpm</p>
                                <p><b>Respiration</b>: {{ $visit->svitals->respiration }} bpm</p>
                            </div>
                        @else
                            <p>No vitals have been recorded for this visit.</p>
                        @endisset
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="py-1"></div>

    <div class="container card">
        <div id="actions-tab" data-tablist="#actions">
            @include('components.tabs', [
                'options' =>
                    $visit->readable_visit_type == 'Antenatal'
                        ? ['Follow Up', 'First Visit', 'Medical Records']
                        : ['Medical Records'],
            ])

            <div id="actions" class="py-1">
                {{-- Antenatal Doings --}}
                @if ($visit->readable_visit_type == 'Antenatal')
                    <div class="tab">
                        @livewire('doctor.anc-visit', ['visit' => $visit])
                    </div>
                    <div class="tab">
                        {{-- @dump($profile) --}}
                        <form action="{{ route('doctor.submit-anc-booking', ['profile' => $profile]) }}"
                            method="post">
                            @csrf
                            <input type="hidden" name="go_back">
                            <div class="grid grid-cols-3 gap-x-3">
                                <div class="w-full">
                                    <div class="form-group">
                                        <label for="gravida">Gravidity</label>
                                        <input type="number" name="gravida" id="gravida" class="form-control"
                                            required value="{{ old('gravida') ?? $profile->gravida }}" />
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="form-group">
                                        <label for="parity">Parity</label>
                                        <input type="number" name="parity" id="parity" class="form-control"
                                            value="{{ old('parity') ?? $profile->parity }}" />
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="form-group">
                                        <label for="fundal_height">Height of Fundus</label>
                                        <input type="text" name="fundal_height" id="fundal_height"
                                            class="form-control"
                                            value="{{ old('fundal_height') ?? $profile->fundal_height }}">
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="form-group">
                                        <label for="fetal_heart_rate">Fetal Heart Rate</label>
                                        <input type="text" name="fetal_heart_rate" id="fetal_heart_rate"
                                            class="form-control"
                                            value="{{ old('fetal_heart_rate') ?? $profile->fetal_heart_rate }}">
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="form-group">
                                        <label for="presentation">Presentation</label>
                                        <input type="text" name="presentation" id="presentation" class="form-control"
                                            value="{{ old('presentation') ?? $profile->presentation }}">
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="form-group">
                                        <label for="lie">Lie</label>
                                        <input type="text" name="lie" id="lie" class="form-control"
                                            value="{{ old('lie') ?? $profile->lie }}">
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="form-group">
                                        <label for="relationship">Relationship of Presenting Part to Pelvis</label>
                                        <input type="text" name="presenting_relationship" id="relationship"
                                            class="form-control"
                                            value="{{ old('presenting_relationship') ?? $profile->presentation_relationship }}">
                                    </div>
                                </div>
                            </div>
                            <div class="my-2">
                                @include('components.tests-form', ['tests' => $tests])
                            </div>
                            @include('components.prescriptions-form')
                            <div class="form-group mt-1">
                                <label for="next_date">Next Visit Date</label>
                                <input type="date" name="next_date" id="next_date" class="form-control" required
                                    value="{{ now()->addWeeks(2)->format('Y-m-d') }}" />
                            </div>
                            <div class="form-group mt-1">
                                <label><input type="checkbox" name="complete" /> Booking Completed</label><br>
                                <button class="btn btn-blue">Submit</button>
                            </div>
                        </form>
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

                    {{-- @livewire('doctor.medical-records', ['visit' => $visit->visit]) --}}
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
                        <form id="test-form">
                            @csrf
                            <div class="form-group">
                                <label>Select Test</label>
                                <livewire:product-search departmentId='5' @selected="addTest($event.detail.id)" />
                            </div>
                            <div class="form-group">
                                <button class="btn bg-blue-500 text-white">Submit</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab">
                        <form id="rad-form">
                            @csrf
                            <div class="form-group">
                                <label>Request Scan</label>
                                {{-- <input type="text" name="scan" id="" required="required"
                                class="form-control w-1/3" list="scans-list"> --}}
                                <livewire:product-search departmentId='7' @selected="addScan($event.detail.id)" />
                            </div>
                            <button class="btn bg-blue-500 text-white">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal hide" id="history-modal">
        <div class="content p-3 bg-white">
            <p class="text-xl bold">Add History</p>
            <div class="py-3"></div>
            <div class="flex gap-x-3">
                <div>
                    <input type="text"
                        class="form-control @error('historyForm.presentation') border-red-500 @enderror"
                        list="histories" placeholder="Presentation" wire:model.live="historyForm.presentation" />
                    @error('historyForm.presentation')
                        <span class="error text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <input type="text" class="form-control @error('historyForm.duration') border-red-500 @enderror"
                        placeholder="Duration" wire:model.live="historyForm.duration" />
                    @error('historyForm.duration')
                        <span class="error text-xs text-red-600">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="py-1"></div>
            <button class="btn btn-blue" wire:click="addHistory">&plus; Add</button>
        </div>
    </div>

    <div class="modal hide" id="exams-modal">
        <div class="content bg-white p-3">
            <p class="text-xl bold">Examination</p>
            <div class="py-2"></div>
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
</div>

@script
    <script>
        asyncForm("#exams-form", "{{ route('doctor.examine', ['visit' => $visit->visit]) }}", async (e, res) => {
            const data = await res.json()
            console.log(data);
        })
    </script>
@endscript
