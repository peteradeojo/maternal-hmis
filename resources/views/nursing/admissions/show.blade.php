@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">
            <p>{{ $admission->patient->name }} (Admitted: {{ $admission->created_at->format('Y-m-d') }})</p>
        </div>
        <div class="header">
            <div class="row">
                <div class="col-6">
                    <p><b>Patient:</b> {{ $admission->patient->name }}</p>
                    <p><b>Age:</b> {{ $admission->patient->dob?->diffInYears() }}</p>
                    <p><b>Category:</b> {{$admission->patient->category->name}}</p>
                    <p><b>Gender:</b> {{ $admission->patient->gender_value }}</p>
                    <p><b>Ward:</b> @unless ($admission->ward)
                        <a href="{{route('nurses.admissions.assign-ward', $admission)}}">Assign to Ward</a>
                    @else
                        {{$admission->ward->name}}
                    @endunless</p>
                    <p><b>Insurance: </b> {{$admission->patient->insurance?->hmo_name ?? "None"}}</p>
                </div>
                <div class="col-6">
                    <p><b>Complaints:</b></p>
                    <ul>
                        @foreach ($admission->admittable->complaints as $complaint)
                            <li>{{ $complaint->name }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-12 py"></div>
                <div class="col-6">
                    <p><b>Diagnosis:</b> {{ $admission->admittable->diagnoses->join(',') }}</p>
                </div>
            </div>
        </div>
        <div class="body py">
            <div class="tabs" data-list="#list">
                <button class="tab-item btn btn-blue" data-target="#admission-plan">Admission Plan</button>
                <button class="tab-item btn btn-blue" data-target="#vitals-tab">Vitals Chart</button>
                <button class="tab-item btn btn-blue" data-target="#treatments-tab">Drug Charts</button>
                <button class="tab-item btn btn-blue">Operation Notes</button>
            </div>
            <div class="tab-list" id="list">
                <div id="admission-plan" class="tab p-1">
                    <h2>Admission Plan</h2>
                    <div class="pb-1"></div>
                    <div>
                        <h3>Drugs</h3>
                        <div class="pt-1"></div>
                        <p><b>Tick boxes to submit administration</b></p>
                        <form action="?log-treatments" method="post">
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
                                    @forelse ($admission->admittable->treatments as $p)
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
                            @if ($admission->admittable->treatments->count() > 0 && $admission->in_ward)
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
                                    <td>{{$adm->treatments}}</td>
                                    <td>{{$adm->created_at?->format('Y-m-d')}}</td>
                                    <td>{{$adm->created_at?->format('h:i A')}}</td>
                                    <td>{{$adm->minister->name}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab p-1 hide" id="vitals-tab">
                    <h2>Vitals Chart</h2>

                    <div class="pt-1"></div>
                    <form action="?vitals" method="post">
                        @csrf
                        <div class="row">
                            @include('nursing.components.vitals-form')
                        </div>
                        <div class="form-group">
                            <button class="btn btn-blue">Submit</button>
                        </div>
                    </form>

                    <div class="py-1"></div>
                    <table class="table-list">
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
                            @forelse ($admission->vitals as $vital)
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
                    </table>
                </div>

                <div class="tab hide" id="treatments-tab">
                    <h3>Treatments</h3>
                </div>
            </div>

        </div>
    </div>
@endsection
