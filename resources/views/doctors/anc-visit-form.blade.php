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
        {{-- <div class="col-4">
            <p class="card-header">Radiology</p>
            <p><b>Height of Fundus: </b> {{ $ancVisit->fundal_height ?? 'Not done' }}</p>
            <p><b>Fetal Heart Rate: </b> {{ $ancVisit->fetal_heart_rate ?? 'Not done' }}</p>
            <p><b>Presentation: </b> {{ $ancVisit->presentation ?? 'Not done' }}</p>
            <p><b>Lie: </b> {{ $ancVisit->lie ?? 'Not done' }}</p>
        </div> --}}
        <div class="col-4">
            <p class="card-header">Vitals</p>
            <p><b>Blood Pressure: </b> {{ $ancVisit->visit->vitals?->data->blood_pressure ?? 'Not recorded' }}</p>
            <p><b>Weight: </b> {{ $ancVisit->visit->vitals?->data->weight ?? 'Not recorded' }}</p>
        </div>
    </div>
</div>
<form action="{{ route('doctor.treat-anc', $ancVisit->id) }}" method="post" class="mt-2">
    @foreach ($errors->all() as $message)
        @dump($message)
    @endforeach
    @csrf
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <label for="fundal_height">Height of Fundus</label>
                <input type="text" name="fundal_height" id="fundal_height" class="form-control" value="{{ old('fundal_height') }}">
            </div>
        </div>
        <div class="col-4 pl">
            <div class="form-group">
                <label for="fetal_heart_rate">Fetal Heart Rate</label>
                <input type="text" name="fetal_heart_rate" id="fetal_heart_rate" class="form-control" value="{{ old('fetal_heart_rate') }}">
            </div>
        </div>
        <div class="col-4 pl">
            <div class="form-group">
                <label for="presentation">Presentation</label>
                <input type="text" name="presentation" id="presentation" class="form-control" value="{{ old('presentation') }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <label for="lie">Lie</label>
                <input type="text" name="lie" id="lie" class="form-control" value="{{ old('lie') }}">
            </div>
        </div>
        <div class="col-4 pl">
            <div class="form-group">
                <label for="relationship">Relationship of Presenting Part to Pelvis</label>
                <input type="text" name="presenting_relationship" id="relationship" class="form-control" value="{{ old('presenting_relationship') }}">
            </div>
        </div>
        <div class="col-4 pl">
            <div class="form-group">
                <label for="return_visit">Return Visit</label>
                <input type="date" name="return_visit" id="return_visit" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('return_visit') }}">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="complaints">Complaints (if any)</label>
        <textarea name="complaints" id="complaints" cols="30" rows="5" class="form-control">{{ old('complaints') }}</textarea>
    </div>
    <div class="form-group">
        <label for="drugs">Drugs (TT,Haem,IPT,Others)</label>
        <textarea name="drugs" id="drugs" cols="30" rows="5" class="form-control">{{ old('drugs') }}</textarea>
    </div>
    <div class="form-group">
        <label for="note">Note (if any)</label>
        <textarea name="note" id="note" cols="30" rows="5" class="form-control">{{ old('note') }}</textarea>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-green">Submit</button>
    </div>
</form>
