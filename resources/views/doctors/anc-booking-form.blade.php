<div>
    <div class="card py px mb-2">
        <div>
            <input type="search" wire:model="patientId" placeholder="Enter Booking ID">
            <button wire:click='load'>Load Booking Form</button>
        </div>

        @if ($profile)
            <div class="my" wire:loading.remove wire:target='load'>
                <p><b>Booking:</b> {{ $profile->patient->name }}</p>
                <form action="{{ route('doctor.submit-anc-booking', $profile) }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="gravida">Gravida</label>
                                <input type="number" name="gravida" id="gravida" class="form-control">
                            </div>
                        </div>
                        <div class="col-4 pl">
                            <div class="form-group">
                                <label for="parity">Parity</label>
                                <input type="number" name="parity" id="parity" class="form-control">
                            </div>
                        </div>
                        <div class="col-4 pl">
                            <div class="form-group">
                                <label for="fundal_height">Height of Fundus</label>
                                <input type="text" name="fundal_height" id="fundal_height" class="form-control"
                                    value="{{ old('fundal_height') }}">
                            </div>
                        </div>
                        <div class="col-4">
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
                        <div class="col-4 pl">
                            <div class="form-group">
                                <label for="lie">Lie</label>
                                <input type="text" name="lie" id="lie" class="form-control"
                                    value="{{ old('lie') }}">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="relationship">Relationship of Presenting Part to Pelvis</label>
                                <input type="text" name="presenting_relationship" id="relationship"
                                    class="form-control" value="{{ old('presenting_relationship') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-blue">Submit</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
