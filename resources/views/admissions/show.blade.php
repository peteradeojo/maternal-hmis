@extends('layouts.app')
@section('title', 'Admission | ' . $data->patient->name)

@section('content')
    @php
        $user = request()->user();
    @endphp
    <x-modal id="vitals-form" title="Record Vitals">
        <livewire:nurses.vitals :event="$data" :showResults="false" />
    </x-modal>

    <x-modal id="discharge-form" title="Discharge Patient">
        <form
            @submit.prevent="submitForm($event.target, '{{ route('api.doctor.discharge', $data) }}').then((res) => {notifySuccess('Patient discharged successfully.');})">
            <div class="form-group">
                <label>Discharge Date</label>
                <x-input-datetime name="discharged_on" class="form-control" />
            </div>
            <div class="form-group">
                <label>Discharge Note</label>
                <x-input-textarea name="discharge_summary" class="form-control" rows="5" />
            </div>
            <div class="form-group">
                <button class="btn bg-blue-400 text-white">Submit</button>
            </div>
        </form>
    </x-modal>

    <div class="container">
        <div class="grid sm:grid-cols-6 gap-6">
            <div class="card col-span-full">
                <x-patient-profile :patient="$data->patient" />
            </div>

            <div class="card col-span-3">
                <div class="card-header">Admission</div>

                <div class="p-3">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Ward</td>
                                <td>{{ $data->ward?->name ?? 'Unassigned' }}</td>
                            </tr>
                            <tr>
                                <td>Admitted by:</td>
                                <td>{{ $data->plan?->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Admitted on:</td>
                                <td>{{ $data->created_at->format('Y-m-d h:i A') }}</td>
                            </tr>
                            <tr>
                                <td>Indication</td>
                                <td>{{ $data->plan?->indication ?? 'No indication' }}</td>
                            </tr>
                            <tr>
                                <td>Treatment Plan</td>
                                <td>
                                    <ul class="list-disc list-inside">
                                        @forelse ($data->plan->prescription?->lines ?? [] as $treatment)
                                            <li class="list-item">{{ $treatment }}</li>
                                        @empty
                                            <li>No plan</li>
                                        @endforelse
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>Tests</td>
                                <td x-data="{ view: false }" class="grid gap-y-2">
                                    @role('doctor')
                                        <button @click="view = !view" class="bg-blue-400 text-white px-4 float-end">
                                            <span x-text="view ? 'Close' : 'View'"></span>
                                        </button>
                                        <div x-show="view" x-transition>
                                            @include('doctors.components.test-results', [
                                                'tests' => $data->tests,
                                            ])
                                        </div>
                                    @else
                                        <ul>
                                            @forelse ($data->tests as $t)
                                                <li>{{ $t->name }}</li>
                                            @empty
                                                <li>No tests</li>
                                            @endforelse
                                        </ul>
                                    @endrole
                                </td>
                            </tr>
                            <tr>
                                <td>Investigations</td>
                                <td>
                                    <ul class="list-disc list-inside">
                                        @forelse ($data->visit->scans ?? [] as $treatment)
                                            <li class="list-item">{{ $treatment }}</li>
                                        @empty
                                            <li>No scan</li>
                                        @endforelse
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>Note</td>
                                <td>{{ $data->plan->note ?? 'nil' }}</td>
                            </tr>
                            <tr>
                                {{-- <td>
                                <a href="{{ route('doctor.show-admission-plan', $data) }}" class="link">View Plan</a>
                            </td> --}}
                                <td>
                                    <a href="#" @click.prevent="$dispatch('open-discharge-form')"
                                        class="link">Discharge</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card col-span-3 max-h-[400px] overflow-y-auto">
                <div class="flex-center justify-between">
                    <p class="card-header">Vitals</p>

                    @role(['nurse', Roles::RegisteredNurse])
                        <button @click="$dispatch('open-vitals-form')" class="btn bg-blue-500 text-white">Add <i
                                class="fa fa-plus"></i></button>
                    @endrole
                </div>
                <livewire:nurses.vitals :event="$data" :form="false" />
            </div>

            <div class="card col-span-full">
                <x-tabs_v2 :options="[
                    $user->hasRole('doctor') ? 'History & Plan' : null,
                    $user->hasRole('nurse') ? 'Admission Plan' : null,
                    $user->hasRole('doctor') ? 'Drug Chart' : null,
                    'Continuation notes',
                    'Operation Notes',
                    'Delivery Note',
                    $user->hasRole('nurse') ? 'Discharge' : null,
                    $user->hasRole('nurse') ? 'Consent Form' : null,
                ]" id="tablist" target="plan_tabs">


                    {{-- History & Plan / Admission Plan --}}
                    @role(Roles::Doctor)
                        <div class="tab p-2">
                            <p class="basic-header">Patient History</p>

                            <div>
                                <table class="table">
                                    <tr>
                                        <th>Presentation</th>
                                        <th>Duration</th>
                                    </tr>
                                    @foreach ($data->visit->histories->merge($data->visit->visit->histories) as $h)
                                        <tr>
                                            <td>{{ $h->presentation }}</td>
                                            <td>{{ $h->duration }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 py-4">
                                <div class="h-[400px] overflow-auto">
                                    <p class="basic-header">History of presenting complaints</p>
                                    @foreach ($data->visit->notes as $note)
                                        <x-doctors-note :note="$note" />
                                    @endforeach
                                </div>
                                <div class="h-[400px] overflow-auto">
                                    <p class="basic-header">Examinations</p>
                                    @unless ($data->visit->examination || $data->visit->visit->examination)
                                        <p>No examination conducted.</p>
                                    @else
                                        @php
                                            $exam = $data->visit->examination ?? $data->visit->visit->examination;
                                        @endphp
                                        <div class="bg-gray-100 p-2">
                                            <p><b>General</b></p>
                                            <p>{{ $exam->general }}</p>
                                        </div>

                                        @foreach ($exam->specifics as $k => $sp)
                                            <div class="bg-gray-100 p-2">
                                                <p><b>{{ unslug($k, fn($str) => ucwords(str_replace('digital', '/', $str))) }}</b>
                                                </p>
                                                <p>{{ $sp ?? '-' }}</p>
                                            </div>
                                        @endforeach
                                    @endunless
                                </div>
                            </div>

                            <p class="basic-header">Admission Plan</p>
                            <livewire:admissions.plan :visit="$data->visit->visit" :admission="$data" />
                        </div>
                    @endrole

                    @role([Roles::Nurse, Roles::RegisteredNurse])
                        <div class="tab p-2">
                            <h2>Admission Plan</h2>
                            <p class="p-1"><b>Indication for admission:</b> {{ $data->plan?->indication }}</p>
                            <div class="py-2">
                                <h2 class="header">Drugs</h2>
                                <p><i><b>NB:</b> Tick boxes to submit administration</i></p>
                                <form action="{{ route('nurses.admissions.show', $data) }}?submit=treatment-log"
                                    method="post">
                                    @csrf
                                    <table class="table-list">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Dosage</th>
                                                <th>Frequency</th>
                                                <th>Duration</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($data->plan?->prescription?->lines ?? [] as $p)
                                                <tr>
                                                    <td>{{ $p->item?->name ?? $p->description }}</td>
                                                    <td>{{ $p->dosage }}</td>
                                                    <td>{{ $p->frequency }}</td>
                                                    <td>{{ $p->duration }}</td>
                                                    <td><input type="checkbox" name="ministered[{{ $p->id }}]"></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">No treatments</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    @if ($data->plan?->prescription?->lines->count() > 0 && $data->in_ward)
                                        <div class="pt-1"></div>
                                        <button type="submit" class="btn btn-red">Submit</button>
                                    @endif
                                </form>
                            </div>

                            <h2 class="header">History</h2>
                            @include('nursing.components.admission-treatments', ['admission' => $data])
                        </div>
                    @endrole

                    {{-- Drug Chart --}}
                    @role('doctor')
                        <div class="tab p-2">
                            <p class="basic-header">Drug delivery chart</p>
                            @include('nursing.components.admission-treatments', ['admission' => $data])
                        </div>
                    @endrole

                    {{-- Continuation notes --}}
                    <div class="tab p-2">
                        <div class="flex-center justify-between">
                            <h2 class="header">Reviews</h2>

                            @role('doctor')
                                <button class="review-btn btn my-2 bg-blue-400 text-white">Review</button>
                            @endrole
                        </div>

                        @forelse ($data->reviews as $note)
                            <x-editable-note :note="$note" />
                        @empty
                            <div class="bg-gray-200 p-1 text-center">No review notes</div>
                        @endforelse
                    </div>

                    {{-- Operation note --}}
                    <div class="tab p-2">
                        <button @click="$dispatch('open-op-note-form')" class="btn bg-green-500 text-white">Create Operation
                            note.</button>

                        <div class="py-2 grid gap-y-2">
                            @forelse ($data->operation_notes as $opnote)
                                <div data-id="{{ $opnote->id }}" class="p-2 border rounded cursor-pointer opnote">
                                    <div>
                                        <b>Procedure: {{ $opnote->procedure_name ?? '' }}</b>
                                        <p>{{ $opnote->procedure }}</p>
                                    </div>
                                    <p><b>Surgeons:</b> {{ $opnote->surgeons }}</p>
                                    <p><small><b>Date: </b>{{ $opnote->created_at->format('Y-m-d h:i A') }}</small></p>
                                </div>
                            @empty
                                <p>No operation note created.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Delivery note --}}
                    <div class="tab p-2" x-data="{ editing: false, data: @js($data) }">
                        <h2 class="header">Delivery Note</h2>

                        @if ($data->patient->category->name != 'Antenatal')
                            <p class="text-red-500 font-semibold">This patient is not an antenatal patient!</p>
                        @endif

                        <template x-cloak x-if="data.delivery_note != undefined">
                            <div class="grid items-center gap-y-2">
                                <div class="border p-2 w-full">
                                    <p>{{ $data->delivery_note?->note }}</p>
                                    <p><b>Taken: </b> {{ $data->delivery_note?->consultant?->name }}</p>
                                    <p>{{ $data->delivery_note?->created_at->format('Y-m-d h:i A') }}</p>
                                </div>

                                <button @click.stop="editing = true" class="btn bg-red-400 text-white btn-sm">Edit</button>
                            </div>

                        </template>

                        <template x-if="editing == true || data.delivery_note == undefined">
                            <livewire:admission.delivery-note :admission="$data" />
                        </template>
                    </div>

                    @role('nurse')
                        {{-- Discharge --}}
                        <div class="tab p-2">
                            <form action="{{ route('nurses.admissions.discharge', $data) }}" method="post"
                                x-data="{ dama: true }">
                                @csrf
                                <template x-if="!dama">
                                    <span>
                                        <div class="form-group">
                                            <label>Discharge Date</label>
                                            <x-input-datetime name="discharged_on" class="form-control"
                                                value="{{ $data->discharged_on }}" />
                                        </div>
                                        <div class="form-group">
                                            <label>Discharge summary</label>
                                            <x-input-textarea name="discharge_summary" rows="5" class="form-control"
                                                value="{{ $data->discharge_summary }}" />
                                        </div>
                                    </span>
                                </template>

                                <template x-if="dama">
                                    <span>
                                        <p class="uppercase text-center basic-header">Discharge Against Medical Advice</p>
                                        <input type="hidden" name="dama" />

                                        <div>
                                            <p>This is to certify that I, <x-input-text required name="name" />. discharged
                                                my <x-input-text name="relationship" required />.
                                                against the advice of the attending physician and of the hospital
                                                administration. I acknowledged that I have been informed of the risks involved
                                                and hereby release the attending physician and hospital from all
                                                responsibilities for ill effects which may result from such discharge.
                                            </p>
                                        </div>
                                        <div class="py-8 grid grid-cols-2 gap-8">
                                            <div>
                                                <label>Patient name</label>
                                                <x-input-text readonly class="form-control" name="patient" placeholder="Patient name"
                                                    value="{{ $data->patient->name }}" required />
                                            </div>
                                            <div>
                                                <label>Patient signature</label>
                                                <x-signature-input name="patient_signature" />
                                            </div>
                                            <div>
                                                <label>Relative name</label>
                                                <x-input-text name="relative_name" />
                                            </div>
                                            <div>
                                                <label>Relationship to patient</label>
                                                <x-input-text name="relative_relationship" />
                                            </div>
                                            <div>
                                                <label>Relative signature</label>
                                                <x-signature-input name="relative_signature" />
                                            </div>
                                            <div></div>
                                            <div>
                                                <label>Nurse</label>
                                                <x-input-text name="nurse" />
                                            </div>
                                            <div>
                                                <label>Nurse Signature</label>
                                                <x-signature-input name="nurse_signature" />
                                            </div>
                                        </div>
                                    </span>
                                </template>
                                <label>
                                    <input type="checkbox" x-on:change="dama = $event.target.checked"
                                        :checked="dama ? 'checked' : ''">
                                    DAMA?
                                </label>
                                <div class="form-group">
                                    <button type="submit" class="btn bg-blue-400 text-white">Discharge <i
                                            class="fa fa-wheelchair-move"></i></button>
                                </div>
                            </form>
                        </div>

                        {{-- Consent form --}}
                        <div class="tab p-2">
                            <x-consent-form :patient="$data->patient" />
                        </div>
                    @endrole
                </x-tabs_v2>

            </div>
        </div>
    </div>

    <x-overlay-modal id="op-note-form" title="New Operation Note">
        <form id="op-note" method="post">
            <div class="grid gap-x-2 grid-cols-2">
                <div class="form-group">
                    <label>Unit</label>
                    <input type="text" name="unit" id="" class="form-control">
                </div>
                <div class="form-group">
                    <label>Consultant</label>
                    <input type="text" name="consultant" value="{{ auth()->user()->name }}" id=""
                        class="form-control">
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-x-2 mt-5">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="operation_date" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Surgeon(s)</label>
                    <input type="text" name="surgeons" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Assistant(s)</label>
                    <input type="text" name="assistants" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Scrub Nurse</label>
                    <input type="text" name="scrub_nurse" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Circulating Nurse</label>
                    <input type="text" name="circulating_nurse" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Indications for Operation</label>
                    <textarea name="indication" required class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Anaesthesist(s)</label>
                    <input type="text" name="anaesthesists" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Type of Anaesthesia</label>
                    <input type="text" name="anaesthesia_type" class="form-control" required />
                </div>
            </div>

            <div class="form-group">
                <label>Incision</label>
                <input type="text" name="incision" class="form-control" />
            </div>

            <div class="form-group">
                <label>Findings</label>
                <textarea name="findings" rows="5" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Procedure</label>
                <x-input-text name="procedure_name" class="form-control" required />
            </div>
            <div class="form-group">
                <label>Procedure Details</label>
                <textarea name="procedure" rows="15" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <button class="btn bg-blue-400 text-white">Submit</button>
            </div>
        </form>
    </x-overlay-modal>

    <x-modal id="discharge-form" title="Discharge Patient">
        <form
            @submit.prevent="submitForm($event.target, '{{ route('api.doctor.discharge', $data) }}').then((res) => {notifySuccess('Patient discharged successfully.');})">
            <div class="form-group">
                <label>Discharge Date</label>
                <x-input-datetime name="discharged_on" class="form-control" />
            </div>
            <div class="form-group">
                <label>Discharge Note</label>
                <x-input-textarea name="discharge_summary" class="form-control" rows="5" />
            </div>


            <div class="form-group">
                <button class="btn bg-blue-400 text-white">Submit</button>
            </div>
        </form>
    </x-modal>
@endsection

@push('scripts')
    <script>
        async function setForDischarge(e) {
            const form = new FormData(e.currentTarget);
            try {
                const res = await axios.delete("{{ route('api.doctor.discharge', $data) }}", {
                    data: form,
                    headers: {
                        'Content-type': 'multipart/form-data'
                    }
                });

                if (res.data.success) {
                    notifySuccess("Patient set for discharge successful.");
                }
            } catch (error) {
                console.error(error);
                notifyError(error.message);
            }
        }

        $(document).ready(() => {
            initTab(document.querySelector("#tablist"));
            initSignatureCanvas();

            asyncForm("#op-note", "{{ route('api.doctor.save-op-note', $data) }}", (e, data) => {
                window.location.reload()
            });

            $(document).on('click', '.opnote', (e) => {
                const {
                    id
                } = $(e.currentTarget).data();

                useGlobalModal((a) => {
                    a.find(MODAL_TITLE).text('Operation Note');
                    a.find(MODAL_BODY).html(`@include('components.spinner')`);
                    axios.get("{{ route('doctor.admission.op-note', ':op') }}".replace(':op', id))
                        .then((res) => {
                            a.find(MODAL_BODY).html(res.data);
                        })
                        .catch(err => {
                            displayNotification({
                                message: "An error occurred",
                                bg: ['bg-red-500', 'text-white'],
                                options: {
                                    mode: 'in-app',
                                }
                            })
                        });
                });
            });

            $(document).on('click', '.review-btn', (e) => {
                useGlobalModal((a) => {
                    a.find(MODAL_TITLE).text("Review")
                    axios.get("{{ route('doctor.admissions.review', $data) }}").then((res) => {
                        a.find(MODAL_BODY).html(res.data);
                    }).catch((err) => {
                        a.find(MODAL_BODY).html(err.response.data);
                    });
                });
            });

            document.querySelector("#consent-form")?.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const data = (form.serializeArray());
                data.push({
                    name: 'signature',
                    value: canvas.toDataURL()
                });

                const serialized = $.param(data);
                console.log(serialized);

                axios.post("{{ route('nurses.admissions.consent-form', $data) }}", serialized, {
                        headers: {
                            'Content-type': 'application/x-www-form-urlencoded'
                        },
                    })
                    .then((res) => {
                        notifySuccess("Consent saved successfully");
                        this.reset();
                        resetCanvas();
                    })
                    .catch(err => {
                        console.error(err);
                        notifyError(err.response.data.message);
                    });
            });
        });
    </script>
@endpush
