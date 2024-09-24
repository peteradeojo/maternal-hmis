<div class="grid grid-cols-3 gap-y-2">
    <p>
        <b>Maturity/Weeks of Gestation: </b>
        {{ $ancProfile->lmp ? $ancProfile->lmp->diffInWeeks() . ' week(s)' : 'LMP Not Supplied' }}
    </p>
    <p><b>EDD: </b> @unless ($ancProfile->edd)
            <input type="date" wire:model.live="editEdd" id="">
            <a href="#" wire:click.prevent="updateEdd">Update<a />
            @else
                {{ $ancProfile->edd->format('Y-m-d') }}
            @endunless
    </p>
    <p>
        @if ($editingLmp)
            <b>LMP: </b>
            <input type="date" wire:model.live="lmpEdit">
            <a href="#" wire:click.prevent="updateLmp">Update</a>
        @else
            <b>LMP: </b> {{ $ancProfile->lmp?->format('Y-m-d') }}
            <a href="#" class="text-blue-600 underline" wire:click.prevent="editLmp">Edit</a>
        @endif
    </p>
    <p><b>Date of Booking: </b>
        {{ $ancProfile->created_at?->format('Y-m-d') }}</p>
    <p><b>Card Type: </b>
        {{ $ancProfile->card_type }}
    </p>
    <p class="p-4"></p>
    <p class=""></p>
    <p class=""></p>
    <p class=""></p>
    <p><b>Gravida: </b> {{ $ancProfile->gravida }}</p>
    <p><b>Parity: </b> {{ $ancProfile->parity }}</p>
    <p><b>Height: </b> {{ $ancProfile->vitals['height'] ?? '' }} cm</p>
    <p><b>Weight: </b> {{ $ancProfile->vitals['weight'] ?? '' }} kg</p>
    <p><b>BP: </b> {{ $ancProfile->vitals['blood_pressure'] ?? '' }} mmHg</p>
    <p><b>HB: </b> {{ $ancProfile->hb }} g/dl</p>
    <p><b>Urine: </b> {{ $ancProfile->urine }}</p>
    <p><b>VDRL: </b> {{ $ancProfile->vdrl }}</p>
    <p><b>HIV: </b> {{ $ancProfile->hiv }}</p>
    <p><b>HEP B: </b> {{ $ancProfile->hep_b }}</p>
    <p><b>HEP C: </b> {{ $ancProfile->hep_c }}</p>
    <p><b>Other: </b> {{ $ancProfile->other }}</p>
</div>
