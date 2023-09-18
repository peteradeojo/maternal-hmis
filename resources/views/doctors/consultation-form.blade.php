@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row my start">
            <div class="col-6">
                <div class="card py px foldable">
                    <div class="header foldable-header">
                        <p class="card-header">Profile</p>
                    </div>
                    <div class="body foldable-body">
                        <div class="my">
                            <p><b>Card Number: </b> {{ $visit->patient->card_number }}</p>
                            <p><b>Name: </b> {{ $visit->patient->name }}</p>
                            <p><b>Gender: </b> {{ $visit->patient->gender_value }}</p>
                            <p><b>Age: </b> {{ $visit->patient->dob?->diffInYears() }}</p>
                            <p><b>Occupation: </b> {{ $visit->patient->occupation }}</p>
                            <p><b>Marital Status: </b> {{ MarriageEnum::tryFrom($visit->patient->marital_status)?->name }}
                            </p>
                            <p><b>Category: </b> {{ $visit->patient->category->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 pl">
                <div class="card foldable py px">
                    <div class="header foldable-header">
                        <div class="card-header">Vitals</div>
                    </div>
                    <div class="body foldable-body">
                        <div class="my">
                            <p><b>Taken By: </b> {{ $visit->vital_staff?->name }}</p>
                            {{-- <p><b>Time: </b>
                                @if (isset($visit->vitals->time))
                                    {{ Carbon::parse($visit->vitals->time)->format('Y-m-d h:i A') }}
                                @endif
                            </p> --}}
                            <p><b>Weight: </b> {{ $visit->vitals->data->weight }} kg</p>
                            <p><b>Height: </b> {{ $visit->vitals->data->height }} cm</p>
                            <p><b>B/P: </b> {{ $visit->vitals->data->blood_pressure }} mmHg</p>
                            <p><b>Respiration: </b> {{ $visit->vitals->data->respiratory_rate }}</p>
                            <p><b>Pulse: </b> {{ $visit->vitals->data->pulse }} b/m</p>
                            <p><b>Temperature: </b> {{ $visit->vitals->data->temperature }} &deg;C</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($visit->patient->category->name == 'Antenatal')
            <div class="card py px">
                <div class="header foldable-header">
                    <div class="card-header">Antenatal Booking</div>
                </div>
                <div class="body foldable-body">
                    <div class="py">
                        <p><b>Date of Booking: </b>
                            {{ $visit->patient->antenatalProfiles[0]->created_at?->format('Y-m-d') }}</p>
                        <p><b>Card Type: </b>
                            {{ $visit->patient->antenatalProfiles[0]->card_type }}
                        </p>
                        <p><b>EDD: </b> {{ $visit->patient->antenatalProfiles[0]->edd }}</p>
                        <p><b>Weeks of Gestation: </b>
                            {{ $visit->patient->antenatalProfiles[0]->lmp }}</p>
                        <p><b>Gravida: </b> {{ $visit->patient->antenatalProfiles[0]->gravida }}</p>
                        <p><b>Parity: </b> {{ $visit->patient->antenatalProfiles[0]->parity }}</p>
                        <p><b>Height: </b> {{ $visit->patient->antenatalProfiles[0]->height }} cm</p>
                        <p><b>Weight: </b> {{ $visit->patient->antenatalProfiles[0]->weight }} kg</p>
                        <p><b>BP: </b> {{ $visit->patient->antenatalProfiles[0]->bp }} mmHg</p>
                        <p><b>HB: </b> {{ $visit->patient->antenatalProfiles[0]->hb }} g/dl</p>
                        <p><b>Urine: </b> {{ $visit->patient->antenatalProfiles[0]->urine }}</p>
                        <p><b>VDRL: </b> {{ $visit->patient->antenatalProfiles[0]->vdrl }}</p>
                        <p><b>HIV: </b> {{ $visit->patient->antenatalProfiles[0]->hiv }}</p>
                        <p><b>HEP B: </b> {{ $visit->patient->antenatalProfiles[0]->hep_b }}</p>
                        <p><b>HEP C: </b> {{ $visit->patient->antenatalProfiles[0]->hep_c }}</p>
                        <p><b>Other: </b> {{ $visit->patient->antenatalProfiles[0]->other }}</p>
                    </div>
                </div>
            </div>

            @php
                $ancProfile = &$visit->patient->antenatalProfiles[0];
            @endphp
            <div class="card my py px foldable">
                <div class="header card-header foldable-header">
                    <p>Antenatal History</p>
                </div>
                <div class="foldable-body body my">
                    <table id="anc-history-table" class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Maturity</th>
                                <th>Presentation</th>
                                <th>Lie</th>
                                <th>Fundal Height</th>
                                <th>Fetal Heart Rate</th>
                                {{-- <th>Edema</th>
                                <th>Protein</th>
                                <th>Glucose</th>
                                <th>VDRL</th>
                                <th>PCV</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $ancProfile->created_at?->format('Y-m-d') }}</td>
                                <td>{{ $ancProfile->created_at?->diffInWeeks($ancProfile->lmp) }} week(s)</td>
                                <td>{{ $ancProfile->presentation }}</td>
                                <td>{{ $ancProfile->lie }}</td>
                                <td>{{ $ancProfile->fundal_height }}</td>
                                <td>{{ $ancProfile->fetal_heart_rate }}</td>
                                {{-- <td>{{ $ancProfile->edema }}</td>
                                <td>{{ $ancProfile->protein }}</td>
                                <td>{{ $ancProfile->glucose }}</td>
                                <td>{{ $ancProfile->vdrl }}</td>
                                <td>{{ $ancProfile->pcv }}</td> --}}
                            </tr>
                            @foreach ($ancProfile->history as $history)
                                <tr>
                                    <td>{{ $history->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $history->created_at->diffInWeeks($ancProfile->lmp) }} week(s)</td>
                                    <td>{{ $history->presentation }}</td>
                                    <td>{{ $history->lie }}</td>
                                    <td>{{ $history->fundal_height }}</td>
                                    <td>{{ $history->fetal_heart_rate }}</td>
                                    {{-- <td>{{ $history->edema }}</td>
                                    <td>{{ $history->protein }}</td>
                                    <td>{{ $history->glucose }}</td>
                                    <td>{{ $history->vdrl }}</td>
                                    <td>{{ $history->pcv }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="card py px foldable">
            <div class="foldable-header header">
                <div class="card-header">
                    Documentation
                </div>
            </div>
            <div class="body foldable-body unfolded">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
                <div class="my">
                    @livewire('doctor.consultation-form', ['visit' => $visit])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $("#anc-history-table")?.DataTable({
            dom: "Brtip",
            language: {
                infoEmpty: "No previous visits found",
                emptyTable: "No previous visits found",
            }
        });
    </script>
@endpush
