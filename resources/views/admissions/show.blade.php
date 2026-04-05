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

    <div class="container grid grid-cols-6 gap-6">
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
                            <td>{{ $data->plan->user->name }}</td>
                        </tr>
                        <tr>
                            <td>Admitted on:</td>
                            <td>{{ $data->created_at->format('Y-m-d h:i A') }}</td>
                        </tr>
                        <tr>
                            <td>Indication</td>
                            <td>{{ $data->plan->indication ?? 'No indication' }}</td>
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

                @role('nurse')
                    <button @click="$dispatch('open-vitals-form')" class="btn bg-blue-500 text-white">Add <i
                            class="fa fa-plus"></i></button>
                @endrole
            </div>
            <livewire:nurses.vitals :event="$data" :form="false" />
        </div>

        <div class="card col-span-full">
            <x-tabs_v2 :options="[
                $user->hasRole('doctor') ? 'History & Plan' : null,
                'Drug Chart',
                $user->hasRole('doctor') ? 'Continuation Notes' : 'Reviews',
                ]" id="tablist" target="plan_tabs">
            </x-tabs_v2>

            {{-- History & Plan / Admission Plan --}}
            <div class="tab p-2">

            </div>
        </div>
    </div>
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
        });
    </script>
@endpush
