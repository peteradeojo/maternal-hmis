<div>
    <div class="grid grid-cols-3 gap-x-2 justify-between">
        <p class="col-span-3 py-1">
            <b>Booking date: </b> {{$profile->created_at->format('Y-m-d')}}
        </p>
        <p>
            <b>LMP: </b>
            @if ($editingLmp)
                <input type="date" wire:model="lmpEdit" />
                <a href="#" wire:click.prevent="updateLmp">Update</a>
            @else
                <span>{{ $profile->lmp?->format('Y-m-d') }}</span>
                <a href="#" class="text-blue-600 underline" wire:click.prevent="editLmp">Edit</a>
            @endif
        </p>

        <p>
            <b>EDD: </b>
            @if ($editingEdd || empty($profile->edd))
                <input type="date" wire:model="editEdd" />
                <a href="#" wire:click.prevent="updateEdd">Update</a>
            @else
                <span>{{ $profile->edd?->format('Y-m-d') }}</span>
                <a href="#" wire:click.prevent="setEditingEdd" class="text-blue-600 underline">Edit</a>
            @endif
        </p>

        <p>
            <b>Maturity Weeks: </b>
            <span>{{ $profile->lmp ? $profile->lmp->diffInWeeks() . ' week(s)' : 'LMP Not Supplied' }}</span>
        </p>
    </div>

    <div class="grid grid-cols-3 gap-y-0">
        @unless ($obsEdit)
            <p><b>Gravida: </b> {{ $profile->gravida }}</p>
            <p><b>Parity: </b> {{ $profile->parity }}</p>

            <div class="col-span-3">
                <a href="#" wire:click.prevent="toggleEditObsData" class="link underline">Edit Obstetric Data</a>
            </div>
        @else
            <p><b>Gravida: </b> <input type="text" wire:model="obsData.gravida" id=""></p>
            <p><b>Parity: </b> <input type="text" wire:model="obsData.parity" id=""></p>

            <div class="col-span-3">
                <a href="#" wire:click.prevent="updateObsData" class="link underline">Update</a>
            </div>
        @endunless

        <p><b>Height: </b> {{ $profile->vitals['height'] ?? '' }} cm</p>
        <p><b>Weight: </b> {{ $profile->vitals['weight'] ?? '' }} kg</p>
        <p><b>BP: </b> {{ $profile->vitals['blood_pressure'] ?? '' }} mmHg</p>
        <p><b>HB: </b> {{ $profile->hb }} g/dl</p>
        <p><b>Urine: </b> {{ $profile->urine }}</p>
        <p><b>VDRL: </b> {{ $profile->vdrl }}</p>
        <p><b>HIV: </b> {{ $profile->hiv }}</p>
        <p><b>HEP B: </b> {{ $profile->hep_b }}</p>
        <p><b>HEP C: </b> {{ $profile->hep_c }}</p>
        <p><b>Other: </b> {{ $profile->other }}</p>
    </div>

    <div class="py-2"></div>

    <div class="py-2">
        <p class="text-xl bold">Special consideration / Risk Assessment</p>

        <textarea name="" id="" class="form-control" rows="5" wire:model="obsData.risk_assessment">{{ $profile->risk_assessment }}</textarea>
        <div class="pt-2"></div>
        <button class="btn bg-blue-500 text-white" wire:click="updateObsData">Update Special Consideration</button>
    </div>

</div>
