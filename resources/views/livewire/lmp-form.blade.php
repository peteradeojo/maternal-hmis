<div class="grid md:grid-cols-2 gap-x-3">
    <div class="form-group">
        <label for="lmp">LMP</label>
        <input type="date" name="lmp" id="lmp" class="form-control" wire:model="lmp" value="{{ old('lmp') }}"
            pattern="dd/mm/YYYY" wire:change="setLMP($event.target.value)" max="{{ date('Y-m-d') }}">
        <div class="pt-1"></div>
    </div>
    <div class="form-group">
        <label for="edd">EDD</label>
        <input type="date" wire:model="edd" wire:change="setEDD($event.target.value)" name="edd" id="edd" value="{{ $edd }}" class="form-control"
            readonly required>
    </div>
</div>
