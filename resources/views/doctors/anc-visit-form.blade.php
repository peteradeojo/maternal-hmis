<div class="container">
    <div class="row">
        <div class="col-4">
            <p class="card-header">Lab</p>
            <p><b>Edema: </b> {{ $ancVisit->edema ?? 'Not done' }}</p>
            <p><b>PCV: </b> {{ $ancVisit->pcv ?? 'Not done' }}</p>
            <p><b>VDRL: </b> {{ $ancVisit->vdrl ?? 'Not done' }}</p>
            <p><b>Protein: </b> {{ $ancVisit->protein ?? 'Not done' }}</p>
            <p><b>Glucose: </b> {{ $ancVisit->glucodse ?? 'Not done' }}</p>
        </div>
        <div class="col-4">
            <p class="card-header">Vitals</p>
            {{-- <p><b>Blood Pressure: </b> {{ $ancVisit->visit->vitals?->data->blood_pressure ?? 'Not recorded' }}</p>
            <p><b>Weight: </b> {{ $ancVisit->visit->vitals?->data->weight ?? 'Not recorded' }}</p> --}}
            @include('doctors.components.vitals', ['visit' => $ancVisit->visit])
        </div>
    </div>

    <div class="py-2"></div>

    <div class="tablist" data-tablist="#tab-list">
        @include('components.tabs', ['options' => ['Follow-up Visit', 'First Visit']])
        <div id="tab-list">
            <div class="tab">
                <form action="{{ route('doctor.treat-anc', $ancVisit->id) }}" method="post" class="mt-2">
                    @foreach ($errors->all() as $message)
                        @dump($message)
                    @endforeach
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="fundal_height">Height of Fundus</label>
                                <input type="text" name="fundal_height" id="fundal_height" class="form-control"
                                    value="{{ old('fundal_height') }}">
                            </div>
                        </div>
                        <div class="col-4 pl">
                            <div class="form-group">
                                <label for="fetal_heart_rate">Fetal Heart Rate</label>
                                <input type="text" name="fetal_heart_rate" id="fetal_heart_rate" class="form-control"
                                    value="{{ old('fetal_heart_rate') }}">
                            </div>
                        </div>
                        <div class="col-4 pl">
                            <div class="form-group">
                                <label for="presentation">Presentation</label>
                                <input type="text" name="presentation" id="presentation" class="form-control"
                                    value="{{ old('presentation') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="lie">Lie</label>
                                <input type="text" name="lie" id="lie" class="form-control"
                                    value="{{ old('lie') }}">
                            </div>
                        </div>
                        <div class="col-4 pl">
                            <div class="form-group">
                                <label for="relationship">Relationship of Presenting Part to Pelvis</label>
                                <input type="text" name="presenting_relationship" id="relationship"
                                    class="form-control" value="{{ old('presenting_relationship') }}">
                            </div>
                        </div>
                        <div class="col-4 pl">
                            <div class="form-group">
                                <label for="return_visit">Return Visit</label>
                                <input type="date" name="return_visit" id="return_visit" class="form-control"
                                    min="{{ date('Y-m-d') }}" value="{{ old('return_visit') }}">
                            </div>
                        </div>
                    </div>
                    <div class="py-2">
                        @include('components.complaints-form')
                        @include('components.tests-form')
                        @include('components.imagings-form')
                        @include('components.prescriptions-form')
                    </div>
                    <div class="form-group">
                        <label for="note">Note (if any)</label>
                        <textarea name="note" id="note" cols="30" rows="5" class="form-control">{{ old('note') }}</textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-green">Submit</button>
                    </div>
                </form>
            </div>
            <div class="tab pt-2">
                {{-- @include('compo') --}}
                @php
                    $profile = $ancVisit->profile;
                @endphp
                <form action="{{ route('doctor.submit-anc-booking', $profile) }}" method="post">
                    @csrf
                    <input type="hidden" name="go_back">
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
                                <input type="text" name="fetal_heart_rate" id="fetal_heart_rate"
                                    class="form-control"
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
                                <input type="text" name="presenting_relationship" id="relationship"
                                    class="form-control"
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
        </div>
    </div>
</div>

{{-- <div class="tabs">
    <div class="tab">

    </div>
    <div class="tab">

    </div>
</div> --}}
@pushOnce('scripts')
    <script>
        initTab(document.getElementsByClassName('tablist')[0]);
    </script>
@endpushOnce
