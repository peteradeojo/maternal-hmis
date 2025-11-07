@extends('layouts.app')

@section('content')
    <x-back-link />

    <x-overlay-modal id="tests">

    </x-overlay-modal>

    <div class="bg-white p-3">
        <div class="pb-3">
            <x-patient-profile :patient="$data->patient" />
        </div>
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
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="p-3">
            <x-tabs_v2 id="tablist" target="plan-tabs" :options="['Vitals Chart', 'Drug Chart']">
                <div class="tab p-2">
                    <p class="text-lg font-semibold">Vitals</p>

                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Date</th>
                                <th>Temperature</th>
                                <th>Blood pressure</th>
                                <th>Pulse</th>
                                <th>Respiration</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data->svitals as $v)
                                <tr>
                                    <td></td>
                                    <td>{{ $v->recorded_date }}</td>
                                    <td>{{ $v->temperature }}</td>
                                    <td>{{ $v->blood_pressure }}</td>
                                    <td>{{ $v->pulse }}</td>
                                    <td>{{ $v->respiration }}</td>
                                    <td>
                                        @if ($v->recorder)
                                            {{ $v->recorder?->firstname[0] }}. {{ $v->recorder?->lastname }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="7">No vitals recorded.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Treatments --}}
                <div class="tab">
                    <p class="text-lg font-bold">Drugs</p>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data->administrations as $a)
                                <tr>
                                    <td>{{ $a }}</td>
                                    <td>{{ $a->minister->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">No administrations have been recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-tabs_v2>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(() => {
            initTab(document.querySelector("#tablist"));
        });
    </script>
@endpush
