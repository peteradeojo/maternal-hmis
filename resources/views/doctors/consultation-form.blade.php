@extends('layouts.app')

@section('title', $visit->patient->name . "| Doctor's Visit")
@section('content')
    <div class="container">
        <div class="row my start">
            <div class="col-4">
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
                            @include('doctors.components.vitals', ['visit' => $visit, 'complaints'])
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 pl">
                <div class="card py px foldable">
                    <div class="header foldable-header">
                        <div class="card-header">History</div>
                    </div>
                    <div class="foldable-body">
                        @for ($i = 1, $n = $visit->patient->visits->count(); $i < $n; $i++)
                            <a href="#" data-target="#history-modal-{{ $visit->patient->visits[$i]->id }}"
                                class="modal-trigger">{{ $visit->patient->visits[$i]->created_at->format('Y-m-d') }}</a>
                            @if ($i >= 6)
                            @break
                        @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>

    {{-- Anc Booking details --}}
    @if ($visit->patient->category->name == 'Antenatal')
        @php
            $ancProfile = $visit->patient->antenatalProfiles[0] ?? null;
        @endphp
        <div class="card py px foldable">
            <div class="header foldable-header">
                <div class="card-header">Antenatal Booking</div>
            </div>
            <div class="body foldable-body unfolded">
                @if ($ancProfile)

                @else
                    <div class="py">
                        <b>This Patient has no active antenatal profile.</b>
                    </div>
                @endif
            </div>
        </div>

        {{-- Anc history --}}
        @if ($ancProfile)
            <div class="card my py px foldable">
                <div class="header card-header foldable-header">
                    <p>Antenatal History</p>
                </div>
                <div class="foldable-body body my unfolded">
                    <table id="anc-history-table" class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Maturity (weeks)</th>
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
                                <td>{{ $ancProfile->lmp?->diffInWeeks() }}</td>
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
    @endif

    <div class="card my py px foldable">
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
                @livewire('doctor.consultation-form', ['visit' => $visit, 'complaints' => $complaints, 'diagnoses' => $diagnoses, 'tests' => $tests, 'prescriptions' => $prescriptions])
            </div>
        </div>
    </div>

    @for ($i = 1, $n = $visit->patient->visits->count(); $i < $n; $i++)
        <div class="modal hide" id="history-modal-{{ $visit->patient->visits[$i]->id }}">
            <div class="content">
                @include('doctors.components.history-report', ['visit' => $visit->patient->visits[$i]])
            </div>
        </div>
        @if ($i >= 6)
            @break
        @endif
    @endfor
</div>
@endsection

@push('scripts')
<script>
    $("#anc-history-table")?.DataTable({
        responsive: true,
        dom: "Brtip",
        language: {
            infoEmpty: "No previous visits found",
            emptyTable: "No previous visits found",
        }
    });
</script>
@endpush
