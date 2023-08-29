<div class="form-group">
    <label for="lmp">LMP</label>
    <input type="date" name="lmp" id="lmp" class="form-control" value="{{ old('lmp') }}"
        wire:change="setLMP($event.target.value)" max="{{ date('Y-m-d') }}">
    <button type="button" class="btn btn-green" wire:click="calculateEDD">Calculate EDD</button>
</div>
<div class="form-group">
    <label for="edd">EDD</label>
    <input type="date" name="edd" id="edd" value="{{ $edd }}" class="form-control" readonly
        required>
</div>
