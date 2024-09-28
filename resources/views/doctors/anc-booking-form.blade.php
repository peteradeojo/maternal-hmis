@extends('layouts.app')

@section('content')
    <div class="card py px mb-1">
        <div class="row start">
            <div class="col-8 p-1">
                <h3 class="bold text-xl">Profile</h3>
                <hr>
                <div class="row start">
                    <div class="col-6">
                        <p class="my-1"><b>Name: </b> {{ $profile->patient->name }}</p>
                        <p class="my-1"><b>Age: </b> {{ $profile->patient->dob?->diffForHumans(syntax: true) }}</p>
                        <p class="my-1"><b>Card Type: </b> {{ $profile->card_type }}</p>
                        <p class="my-1"><b>Gestational Age: </b> {{ $profile->lmp->diffInWeeks() }} week(s)</p>
                        <p class="my-1"><b>LMP: </b> {{ $profile->lmp->format('Y-m-d') }}</p>
                        <p class="my-1"><b>EDD: </b> {{ $profile->edd->format('Y-m-d') }}</p>
                    </div>
                    <div class="col-6">
                        @forelse ($profile->vitals ?? [] as $v => $r)
                            <p class="my-1"><b>{{ ucfirst(str_replace('_', ' ', $v)) }}: </b> {{ $r }}</p>
                        @empty
                            <p class="my-1"><b>No vitals recorded yet</b></p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-4 p-1">
                <h3 class="bold text-xl">Lab</h3>
                @include('doctors.components.test-results', ['tests' => $profile->tests])
            </div>
        </div>
    </div>
    <div class="card py px">
        <p><b>Booking:</b> {{ $profile->patient->name }}</p>
        @foreach ($errors->all() as $message)
            <p>{{ $message }}</p>
        @endforeach
        <form action="{{ route('doctor.submit-anc-booking', $profile) }}" method="post">
            @csrf
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label for="gravida">Gravidity</label>
                        <input type="number" name="gravida" id="gravida" class="form-control" required
                            value="{{ old('gravida') ?? $profile->gravida }}" />
                    </div>
                </div>
                <div class="col-4 pl">
                    <div class="form-group">
                        <label for="parity">Parity</label>
                        <input type="number" name="parity" id="parity" class="form-control"
                            value="{{ old('parity') ?? $profile->parity }}" />
                    </div>
                </div>
                <div class="col-4 pl">
                    <div class="form-group">
                        <label for="fundal_height">Height of Fundus</label>
                        <input type="text" name="fundal_height" id="fundal_height" class="form-control"
                            value="{{ old('fundal_height') ?? $profile->fundal_height }}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="fetal_heart_rate">Fetal Heart Rate</label>
                        <input type="text" name="fetal_heart_rate" id="fetal_heart_rate" class="form-control"
                            value="{{ old('fetal_heart_rate') ?? $profile->fetal_heart_rate }}">
                    </div>
                </div>
                <div class="col-4 pl">
                    <div class="form-group">
                        <label for="presentation">Presentation</label>
                        <input type="text" name="presentation" id="presentation" class="form-control"
                            value="{{ old('presentation') ?? $profile->presentation }}">
                    </div>
                </div>
                <div class="col-4 pl">
                    <div class="form-group">
                        <label for="lie">Lie</label>
                        <input type="text" name="lie" id="lie" class="form-control"
                            value="{{ old('lie') ?? $profile->lie }}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="relationship">Relationship of Presenting Part to Pelvis</label>
                        <input type="text" name="presenting_relationship" id="relationship" class="form-control"
                            value="{{ old('presenting_relationship') ?? $profile->presentation_relationship }}">
                    </div>
                </div>
            </div>
            <div class="my-2">
                @include('components.tests-form')
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
@endsection
