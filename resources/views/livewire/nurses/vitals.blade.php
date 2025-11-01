<div @closeModal="removeGlobalModal">
    <form wire:submit.prevent="save" x-data="{
        vitals: @entangle('vitals')
    }">
        <div class="grid gap-x-3 grid-cols-3">
            <div class="form-group">
                <label>Date *</label>
                <input type="datetime-local" x-model="vitals.recorded_date" required class="form-control" />
                @error('vitals.recorded_date')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror

            </div>
            <div class="form-group">
                <label for="temperature">Temperature (&deg;C)</label>
                <input type="number" class="form-control" step="0.1" x-model.number="vitals.temperature">
                @error('vitals.temperature')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="pulse">Pulse (b/m)</label>
                <input type="number" class="form-control" x-model.number="vitals.pulse" />
                @error('vitals.pulse')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="respiratory_rate">Respiratory Rate (c/m)</label>
                <input type="number" class="form-control" x-model.number="vitals.respiration">
                @error('vitals.respiration')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="blood_pressure">Blood Pressure (mmHg)</label>
                <input type="text" class="form-control" x-model="vitals.blood_pressure" />
                @error('vitals.blood_pressure')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="weight">Weight (kg)</label>
                <input type="number" class="form-control" step="0.1" x-model.number="vitals.weight" />
                @error('vitals.weight')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="height">Height (cm)</label>
                <input type="number" class="form-control" step="0.1" x-model="vitals.height" />
                @error('vitals.height')
                    <p class="text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <button wire:click="save" class="btn bg-blue-400 text-white">Save <i class="fa fa-save"></i></button>
        </div>
    </form>

    <div class="py-1">
        <table class="table-list">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Blood Pressure (mmHg)</th>
                    <th>Temperature (&deg;C)</th>
                    <th>Pulse (b/m)</th>
                    <th>Respiration (c/m)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($evt->svitals as $vital)
                    <tr>
                        <td>{{ ($vital->recorded_date ?? $vital->created_at)->format('d/m/Y h:i A') }}</td>
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
</div>
