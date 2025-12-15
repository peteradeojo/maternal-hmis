<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

    <form wire:submit.prevent="save" method="post">
        <div class="form-group">
            <label>Note</label>
            <x-input-textarea name="note" wire:model="note" required />
        </div>
        <livewire:doctor.add-prescription :visit="$admission->plan" :dispatch="true" :display="true"
            @prescription_selected="addPrescription($event.detail)" />

        <div class="form-group">
            <ul class="list-disc px-4">
                @forelse ($prescriptions as $p)
                    <li>{{ "{$p['name']} {$p['dosage']} {$p['frequency']} for {$p['duration']} day(s)" }}</li>
                @empty
                    <li>No prescriptions added.</li>
                @endforelse
            </ul>
        </div>

        <div class="form-group">
            <button class="btn bg-green-500 text-white"><i class="fa fa-save"></i> Save</button>
        </div>
    </form>

</div>
