<div>
    {{-- In work, do what you enjoy. --}}
    <x-overlay-modal id="admit" title="Admitting: {{ $visit->patient->name }}">

        <datalist id="patient-diagnoses">
            @foreach ($visit->diagnoses as $d)
                <option value="{{ $d->diagnoses }}">{{ $d->diagnoses }}</option>
            @endforeach
        </datalist>

        <form action="{{ route('doctor.admit', $visit) }}" method="post" id="start-admission-form">
            @csrf
            <div class="form-group">
                <label class="required">Indication for Admission</label>
                <input type="text" name="indication" class="form-control" list="patient-diagnoses" required />
            </div>
            <div class="form-group">
                <label>Notes/Further Instructions</label>
                <textarea name="note" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <button class="btn bg-red-500 text-white">Submit</button>
            </div>
        </form>
    </x-overlay-modal>

    <div class="p-4 bg-white">
        <div id="nav-tab" data-tablist="#tablist">
            <div id="tablist" class="py-1">
                <x-patient-profile :patient="$visit->patient"></x-patient-profile>

                @if ($visit->type == 'Antenatal' || strtolower($visit->patient->category->name) == 'antenatal')
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
        <div wire:ignore.self id="actions-tab" data-tablist="#actions">
            <div class="flex justify-between items-center">
                @include('components.tabs', [
                    'options' =>
                        $visit->type == 'Antenatal'
                            // ? ['First Visit', 'Follow Up', 'Medical Records']
                            ? [$visit->first_visit ? 'First Visit' : 'Follow Up', 'Medical Records']
                            : ['Medical Records'],
                ])

                <button class="btn bg-blue-500 text-white btn-sm" id="view-history">View
                    History</button>
            </div>

            <div id="actions" class="p-2">
                @if ($visit->type == 'Antenatal')
                    @if ($visit->first_visit)
                        <div class="tab">
                            <livewire:doctor.anc-booking :profile="$profile" :visit="$visit->visit" />
                        </div>
                    @else
                        <div class="tab">
                            @livewire('doctor.anc-visit', ['visit' => $visit->visit])
                        </div>
                    @endif
                @endif

                <div class="tab">
                    <button class="btn btn-blue btn-sm" @click="$dispatch('open-history-modal')">Presenting
                        Complaints</button>

                    <button class="btn btn-blue btn-sm" @click="$dispatch('open-notes-modal')">Add Note</button>
                    @if ($visit->examination)
                        <button class="btn btn-green btn-sm" @click="$dispatch('open-exams-modal')">Edit
                            Examination</button>
                    @else
                        <button class="btn btn-blue btn-sm" @click="$dispatch('open-exams-modal')">Add
                            Examination</button>
                    @endif

                    <button class="btn btn-blue btn-sm" @click="$dispatch('open-tests-modal')">Add
                        Investigation</button>
                    <button class="btn btn-blue btn-sm" @click="$dispatch('open-diagnosis-modal')">Add
                        Diagnosis</button>
                    <button class="btn btn-blue btn-sm" @click="$dispatch('open-prescriptions-modal')">Add
                        Prescription</button>

                    <livewire:doctor.medical-records :visit="$visit" />
                </div>
            </div>
        </div>
    </div>

    <x-overlay-modal id="history-modal" title="History/Presentation">
        {{-- Add History --}}
        <form wire:submit.prevent="addHistory">
            <div class="flex gap-x-3">
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
    </x-overlay-modal>

    <x-overlay-modal id="exams-modal" title="Patient Examination">
        <form action="{{ route('doctor.examine', ['visit' => $visit->visit]) }}" id="exams-form" method="post">
            @csrf
            @include('components.examinations-form', ['exam' => $visit->examination])

            <button type="submit" class="btn btn-blue">Submit &triangleright;</button>
        </form>
    </x-overlay-modal>

    <x-overlay-modal id="prescriptions-modal" title="Add Prescription">
        @livewire('doctor.add-presciption', ['visit' => $visit])
    </x-overlay-modal>

    <x-overlay-modal id="tests-modal" title="Request Investigations">
        <div class="">
            <p class="text-lg font-semibold">Lab Tests</p>
            <div class="form-group">
                <label>Select Test</label>
                <livewire:dynamic-product-search departmentId='5' @selected="addTest($event.detail)"
                    @selected_temp="addTest($event.detail)" />
            </div>
        </div>

        <div class="">
            <p class="text-lg font-semibold">Radiology</p>
            <div class="form-group">
                <label>Request Scan</label>
                <livewire:dynamic-product-search departmentId='7' @selected="addScan($event.detail.id)" />
            </div>
        </div>
    </x-overlay-modal>

    <datalist id="histories">
        @foreach ($histories as $history)
            <option value="{{ $history->presentation }}">{{ $history->presentation }}</option>
        @endforeach
    </datalist>
</div>

@script
    <script>
        initTab(document.querySelector('#actions-tab'));

        asyncForm("#exams-form", "{{ route('doctor.examine', ['visit' => $visit]) }}", async (e, res) => {
            const {
                data
            } = res;
            notifySuccess("Examination saved for visit #{{ $visit->id }}");
        });

        asyncForm("#start-admission-form", "{{ route('doctor.admit', $visit) }}", (e, res) => {
            const {
                data
            } = res;

            if (!data.ok) {
                notifyError(data.message);
                return;
            }

            notifySuccess(`Admission process started for ${data.patient.name}`);
            $wire.dispatch('close-admit');
        });

        $(document).ready(() => {
            $(document).on("click", "#view-history", (e) => {
                axios.get(`{{ route('patient.medical-history', ':id') }}`.replace(':id',
                    {{ $visit->patient->id }})).then((res) => {
                    useGlobalModal((a) => {
                        a.find(".modal-title").text("Medical History")
                        a.find(".modal-body").html(res.data);
                    });
                }).catch((err) => {
                    console.log(err.response.data);
                    displayNotification({
                        message: err.response.data,
                        bg: ['bg-red-500', 'text-white'],
                        options: {
                            mode: 'in-app'
                        }
                    })
                })
            });
        });
    </script>
@endscript
