@extends('layouts.app')

@section('content')
    <x-back-link />

    <div class="bg-white p-3">
        <div class="pb-3">
            <x-patient-profile :patient="$data->patient">
                <div class="grid grid-cols-2">
                    <p><b>Age: </b> {{ floor($data->patient->dob?->diffInYears()) }} years</p>
                    <p><b>Ward: </b> {{ $data->ward?->name }}</p>
                </div>
            </x-patient-profile>
        </div>
        <div class="p-3">
            <table class="table">
                <tbody>
                    {{-- <tr>
                        <td></td>
                        <td>{{ $data->visit->id }}</td>
                    </tr> --}}
                    <tr>
                        <td>Ward</td>
                        <td>{{ $data->ward?->name ?? 'Unassigned' }}</td>
                    </tr>
                    <tr>
                        <td>Admitted by:</td>
                        <td>{{ $data->plan->user->name }}</td>
                    </tr>
                    <tr>
                        <td>Admitted on:</td>
                        <td>{{ $data->created_at->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <td>Indication</td>
                        <td>{{ $data->plan->indication ?? 'No indication' }}</td>
                    </tr>
                    <tr>
                        <td>Treatment Plan</td>
                        <td>
                            <ul class="list-disc list-inside">
                                @forelse ($data->plan->treatments ?? [] as $treatment)
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
                            <button @click="view = !view" class="bg-blue-400 text-white px-4 float-end">
                                <span x-text="view ? 'Close' : 'View'"></span>
                            </button>
                            <div x-show="view" x-transition>
                                @include('doctors.components.test-results', ['tests' => $data->tests])
                            </div>
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
                        <td>
                            <a href="{{ route('doctor.show-admission-plan', $data) }}" class="link">View Plan</a>
                        </td>
                        <td>
                            <a href="#" @click="$dispatch('open-discharge-form')" class="link">Discharge</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="p-3">
            <x-tabs_v2 id="tablist" target="plan-tabs" :options="['Vitals Chart', 'Drug Chart', 'Continuation notes', 'Operation Notes', 'Delivery Note']" :active="1">
                {{-- Vitals --}}
                <div class="tab p-2">
                    <p class="text-lg font-semibold">Vitals</p>
                    <livewire:nurses.vitals :event="$data" :form=false />
                </div>

                {{-- Treatments --}}
                <div class="tab p-2">
                    <p class="text-lg font-bold">Drug delivery chart</p>

                    @include('nursing.components.admission-treatments', ['admission' => $data])
                </div>

                {{-- Reviews/Continuations --}}
                <div class="tab p-2">
                    <div class="flex-center justify-between">
                        <h2 class="header">Reviews</h2>
                        <button class="review-btn btn my-2 bg-blue-400 text-white">Review</button>
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
                                <p><b>Procedure:</b> {{ $opnote->procedure }}</p>
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
                    {{-- @if ($data->delivery_note)
                    @else
                        <livewire:admission.delivery-note :admission="$data" />
                    @endif --}}
                </div>
            </x-tabs_v2>
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
        });
    </script>
@endpush
