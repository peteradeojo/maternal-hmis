<div>
    <form wire:submit.prevent="startVisit" method="post">
        <div class="form-group">
            <label>Consultant</label>
            <select wire:model.live="consultant" class="form-control">
                @foreach ($consultants as $consultant)
                    <option wire:key="{{$consultant->id}}" value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                @endforeach
            </select>
            @error('consultant_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>Visit Type</label>
            <select name="visit_type" wire:model="visit_type" class="form-control">
                <option value="1">GENERAL</option>
                <option value="2">ANTENATAL</option>
            </select>
            @error('visit_type')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="btn bg-blue-200">Start visit</button>
        </div>
    </form>
</div>
