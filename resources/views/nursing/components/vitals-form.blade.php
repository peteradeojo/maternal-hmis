<div class="grid gap-x-3 grid-cols-4">
    <div class="form-group">
        <label for="temperature">Temperature (&deg;C)</label>
        <input type="number" name="temperature" id="temperature" class="form-control"
            value="{{ $profile->vitals['temperature'] ?? '' }}" step="0.1">
    </div>
    <div class="form-group">
        <label for="pulse">Pulse (b/m)</label>
        <input type="number" name="pulse" id="pulse" class="form-control"
            value="{{ $profile->vitals['temperature'] ?? '' }}">
    </div>
    <div class="form-group">
        <label for="respiratory_rate">Respiratory Rate (c/m)</label>
        <input type="number" name="respiratory_rate" id="respiratory_rate" class="form-control"
            value="{{ $profile->vitals['temperature'] ?? '' }}">
    </div>
    <div class="form-group">
        <label for="blood_pressure">Blood Pressure (mmHg)</label>
        <input type="text" name="blood_pressure" id="blood_pressure" class="form-control"
            value="{{ $profile->vitals['temperature'] ?? '' }}">
    </div>
    <div class="form-group">
        <label for="spo2">SPO<sub>2</sub></label>
        <input type="number" name="spo2" class="form " />
    </div>
    <div class="form-group">
        <label for="spo2">Fetal Heart Rate (FHR)</label>
        <input type="number" name="fetal_heart_rate" class="form " />
    </div>
    @unless (isset($admission))
        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" name="weight" id="weight" class="form-control" value="{{ old('weight') }}"
                step="0.1">
        </div>
        <div class="form-group">
            <label for="height">Height (cm)</label>
            <input type="number" name="height" id="height" class="form-control" value="{{ old('height') }}"
                step="0.1">
        </div>
    @endunless
</div>
