@extends('layouts.app')

@section('content')
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
                @endif
                <div class="tab">
                    <p class="bold mb-1 text-xl">Vitals</p>
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
            </div>
        </div>
    </div>

    <div class="py-1"></div>

    <div class="container card">
        <div id="actions-tab" data-tablist="#actions">
            @include('components.tabs', [
                'options' => array_merge(
                    ['Medical Records', 'Diagnoses'],
                    $visit->readable_visit_type == 'Antenatal' ? ['First Visit', 'Follow Up'] : []),
            ])

            <div id="actions" class="py-1">
                <div class="tab">
                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#notes-modal">Add Note</button>
                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#diagnosis-modal">Add Diagnosis</button>
                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#tests-modal">Add
                        Investigation</button>
                    <button class="btn btn-blue btn-sm modal-trigger" data-target="#prescriptions-modal">Add
                        Prescription</button>

                    @livewire('doctor.medical-records', ['visit' => $visit])
                </div>
                <div class="tab">
                    <button class="btn btn-red btn-sm modal-trigger" data-target="#diagnosis-modal">Add Diagnosis</button>

                </div>
                @if ($visit->readable_visit_type == 'Antenatal')
                    @php
                        $profile = $visit->patient->antenatalProfiles[0];
                    @endphp

                    <div class="tab">
                        {{-- @dump($profile) --}}
                        <form action="{{ route('doctor.submit-anc-booking', ['profile' => $profile]) }}" method="post">
                            @csrf
                            <input type="hidden" name="go_back">
                            <div class="grid grid-cols-3 gap-x-3">
                                <div class="w-full">
                                    <div class="form-group">
                                        <label for="gravida">Gravidity</label>
                                        <input type="number" name="gravida" id="gravida" class="form-control" required
                                            value="{{ old('gravida') ?? $profile->gravida }}" />
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
                                        <input type="text" name="fundal_height" id="fundal_height" class="form-control"
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
                    <div class="tab">
                        <p class="text-xl bold">Follow Up</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal hide" id="diagnosis-modal">
        <div class="content bg-white p-2">
            <p class="text-xl bold">Add a Diagnosis</p>
            <div class="py-1"></div>
            <form id="diagnosis-form">
                @csrf
                <div class="form-group">
                    <label>Diagnosis</label>
                    <input type="text" placeholder="Enter your diagnosis" name="diagnosis" class="form-control"
                        required list="diagnosis-list" />
                    <datalist id="diagnosis-list">
                        @foreach ($diagnoses as $d)
                            <option>$d</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group">
                    <button class="btn bg-blue-500 text-white">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal hide" id="notes-modal">
        <div class="content p-2 bg-white">
            <p class="bold">Add Note</p>
            <div class="py-1"></div>

            <div id="notes-tabs" data-tablist="#bose">
                @include('components.tabs', ['options' => ['Add', 'Edit']])

                <div id="bose" class="p-1">
                    <div class="tab">
                        <form id="note-form">
                            @csrf
                            <div class="form-group">
                                <label>Note</label>
                                <textarea name="note" class="w-full resize-y border border-gray-400 rounded-none form-textarea" rows="5"
                                    required></textarea>
                            </div>
                            <div class="form-group">
                                <button class="btn bg-blue-500 text-white">Submit</button>
                            </div>
                        </form>

                    </div>
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
                                <input type="text" name="test" required class="form-control w-1/3"
                                    list="tests-list">
                                <datalist id="tests-list">
                                    @foreach ($tests as $test)
                                        <option>{{ $test }}</option>
                                    @endforeach
                                </datalist>
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
                                <input type="text" name="scan" id="" required="required"
                                    class="form-control w-1/3" list="scans-list">
                            </div>
                            <button class="btn bg-blue-500 text-white">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushOnce('scripts')
    <script defer>
        // initTab(document.querySelector('#nav-tab'));
        initTab(document.querySelector('#actions-tab'));
        initTab(document.querySelector('#notes-tabs'));
        initTab(document.querySelector('#tests-tabs'));

        function asyncForm(form, route, callback = (e, data) => {}) {
            $(form).on("submit", (e) => {
                e.preventDefault();
                fetch(route, {
                    method: 'POST',
                    body: new FormData(e.currentTarget),
                    headers: {
                        'Accept': 'application/json',
                    },
                }).then((res) => {
                    callback(e.currentTarget, res);
                }).catch((err) => {
                    console.error(err);
                });
            });
        }

        $(() => {
            $("#note-form").on('submit', (e) => {
                e.preventDefault();
                fetch("{{ route('api.doctor.note', ['visit' => $visit->id]) }}", {
                    method: 'POST',
                    body: new FormData(e.currentTarget),
                }).then((res) => e.currentTarget.closest(".modal").classList.add("hide")).catch((
                    err) => {
                    console.error(err);
                });
            });

            $("#diagnosis-form").on('submit', (e) => {
                e.preventDefault();
                fetch("{{ route('api.doctor.diagnosis', ['visit' => $visit->id]) }}", {
                    method: 'POST',
                    body: new FormData(e.currentTarget),
                }).then((res) => {
                    e.currentTarget.closest(".modal").classList.add("hide");
                }).catch((err) => {
                    console.error(err);
                });
            });

            asyncForm("#test-form", "{{ route('api.doctor.add-test', ['visit' => $visit]) }}", (e, data) => e
                .closest('.modal').classList.add("hide"));
            asyncForm("#rad-form", "{{ route('api.doctor.add-scan', ['visit' => $visit]) }}", (e, data) => e
                .closest('.modal').classList.add("hide"));
        });

        function addPrescription(data) {
            console.log(data);
        }
    </script>
@endpushOnce
