<div class="form-group">
    <label for="cardType">Card Type</label>
    <select name="card_type" id="cardType" class="form-control">
        <option value="1">Bronze</option>
        <option value="2">Silver</option>
        <option value="3">Gold</option>
        <option value="4">Diamond</option>
        <option value="5">Platinum</option>
    </select>
</div>
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
