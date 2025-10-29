<div>
    @unless ($patient->visits->count() > 0 && $patient->visits[0]->status == Status::active->value)
        <form wire:submit.prevent="startVisit" method="post">
            <div class="form-group">
                <label>Consultant</label>
                <select name="consultant" wire:model="consultant" class="form-control">
                    @foreach ($consultants as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('consultant')
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
    @else
        <p>Patient still has an active visit.</p>
    @endunless
</div>
