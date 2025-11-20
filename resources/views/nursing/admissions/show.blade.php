@extends('layouts.app')
@section('title', 'Portal')

@section('content')
    <div class="card py px">
        <div class="header">
            <x-patient-profile :patient="$admission->patient">
                <p>Admitted: {{ $admission->created_at->format('Y-m-d') }}</p>
            </x-patient-profile>
        </div>
        <div class="body py">
            <div id="actions-tab" data-tablist="#list">
                @include('components.tabs', [
                    'options' => [
                        'Admission Plan',
                        'Vitals Chart',
                        'Reviews',
                        'Operation Notes',
                        'Delivery Note',
                        'Discharge',
                    ],
                ])

                <div id="list">
                    {{-- Plan --}}
                    <div id="admission-plan" class="tab p-1">
                        <h2>Admission Plan</h2>
                        <div class="pb-1"></div>
                        <div>
                            <h3>Drugs</h3>
                            <div class="pt-1"></div>
                            <p><b>Tick boxes to submit administration</b></p>
                            <form action="?submit=treatment-log" method="post">
                                @csrf
                                <table class="table-list">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Route</th>
                                            <th>Dosage</th>
                                            <th>Frequency</th>
                                            <th>Duration</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($admission->plan->treatments ?? [] as $p)
                                            <tr>
                                                <td>{{ $p->name }}</td>
                                                <td>{{ $p->route }}</td>
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
                                @if ($admission->plan->treatments?->count() > 0 && $admission->in_ward)
                                    <div class="pt-1"></div>
                                    <button type="submit" class="btn btn-red">Submit</button>
                                @endif
                            </form>
                        </div>
                        <div class="pb-1"></div>
                        <h3>History</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Treatments</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Administered By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admission->administrations as $adm)
                                    <tr>
                                        <td>{{ $adm->treatments }}</td>
                                        <td>{{ $adm->created_at?->format('Y-m-d') }}</td>
                                        <td>{{ $adm->created_at?->format('h:i A') }}</td>
                                        <td>{{ $adm->minister->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Vitals --}}
                    <div class="tab p-1">
                        <h2>Vitals Chart</h2>

                        <div class="pt-1"></div>
                        {{-- <form action="?submit=vitals" method="post">
                            @csrf
                            <div class="row">
                                @include('nursing.components.vitals-form')
                            </div>
                            <div class="form-group">
                                <button class="btn btn-blue">Submit</button>
                            </div>
                        </form> --}}
                        <livewire:nurses.vitals :event="$admission" />

                        <div class="py-1"></div>
                        {{-- <table class="table-list">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Blood Pressure (mmHg)</th>
                                    <th>Temperature (&deg;C)</th>
                                    <th>Pulse (b/m)</th>
                                    <th>Respiration (c/m)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($admission->vitals ?? [] as $vital)
                                    <tr>
                                        <td>{{ $vital->created_at->format('d/m/Y h:i A') }}</td>
                                        <td>{{ $vital->blood_pressure }}</td>
                                        <td>{{ $vital->temperature }}</td>
                                        <td>{{ $vital->pulse }}</td>
                                        <td>{{ $vital->respiration }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" align="center">No vitals recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table> --}}
                    </div>

                    <div class="tab p-1">
                        <h2 class="header">Reviews</h2>

                        <div class="grid gap-y-2">
                            @forelse ($admission->reviews as $review)
                                <div class="p-2 bg-gray-50">
                                    <p>{{ $review->note }}</p>
                                    <p><small><b>Dr. {{ $review->consultant->name }}</b></small></p>
                                    <p><small><b>Date:</b> {{ $review->created_at->format('Y-m-d h:i A') }}</small></p>
                                </div>
                            @empty
                                <p>No reviews posted for this admission.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="tab p-1">
                        <h2 class="header">Operation Notes</h2>

                        <div class="grid gap-y-2">
                            @forelse ($admission->operation_notes as $note)
                                <div data-id="{{ $note->id }}" class="p-2 rounded border hover:bg-gray-100 opnote">
                                    <p><b>Procedure: </b> {{ $note->procedure }}</p>
                                    <p><b>Operation Date: </b> {{ $note->operation_date }}</p>
                                    <p class="text-sm"><b>Date: </b> {{ $note->created_at->format('Y-m-d h:i A') }}</p>
                                </div>
                            @empty
                                <p>No operation notes have been added.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Delivery note --}}
                    <div class="tab p-2">
                        <h2 class="header">Delivery note</h2>

                        @if ($admission->patient->anc_profile)
                            @unless ($admission->delivery_note)
                                <livewire:admission.delivery-note :admission="$admission" />
                            @else
                                <div class="border p-2">
                                    <p>{{ $admission->delivery_note->note }}</p>
                                    <p><b>Taken: </b> {{ $admission->delivery_note->consultant?->name }}</p>
                                    <p>{{ $admission->delivery_note->created_at->format('Y-m-d h:i A') }}</p>
                                </div>
                            @endunless
                        @endif
                    </div>

                    <div class="tab p-1">
                        <h2>Discharge</h2>

                        <form action="{{ route('nurses.admissions.discharge', $admission) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label>Discharge Date</label>
                                <input type="datetime-local" name="discharged_on" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <label>Discharge summary</label>
                                <textarea name="discharge_summary" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="form-group flex justify-end">
                                <button class="btn bg-blue-400 text-white">Discharge <i
                                        class="fa fa-wheelchair-move"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- <nav class="tabs">
                <button class="tab-item btn btn-blue" data-target="#admission-plan">Admission Plan</button>
                <button class="tab-item btn btn-blue" data-target="#vitals-tab">Vitals Chart</button>
                <button class="tab-item btn btn-blue" data-target="#treatments-tab">Drug Charts</button>
                <button class="tab-item btn btn-blue">Operation Notes</button>
            </nav> --}}
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            initTab(document.querySelector('#actions-tab'));

            $(document).on('click', '.opnote', (e) => {
                const {
                    id
                } = $(e.currentTarget).data();

                axios.get("{{ route('doctor.admission.op-note', ':id') }}".replace(':id', id)).then((
                    res) => {
                    useGlobalModal((a) => {
                        a.find(MODAL_TITLE).text('Operation Note');
                        a.find(MODAL_BODY).html(res.data);
                    });
                }).catch((err) => {
                    notifyError(err.message);
                })
            });
        });
    </script>
@endpush
