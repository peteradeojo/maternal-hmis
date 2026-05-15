<div>
    <div class="grid grid-cols-3 gap-x-2">
        {{-- <p>Profile ID: {{ $profile }}</p> --}}
        <p class="col-span-3 py-1">
            <b>Booking date: </b> {{ $profile->created_at->format('Y-m-d') }}
        </p>
        <p>
            <b>LMP: </b>
            @if ($editingLmp)
                <input type="date" wire:model="lmpEdit" />
                <a href="#" class="btn bg-primary text-white" wire:click.prevent="updateLmp">Update</a>
            @else
                <span>{{ $profile->lmp?->format('Y-m-d') }}</span>
                <a href="#" class="text-blue-600 underline" wire:click.prevent="editLmp">Edit</a>
            @endif
        </p>

        <p>
            <b>EDD: </b>
            @if ($editingEdd || empty($profile->edd))
                <input type="date" wire:model="editEdd" />
                <a href="#" class="btn bg-primary text-white" wire:click.prevent="updateEdd">Update</a>
            @else
                <span>{{ $profile->edd?->format('Y-m-d') }}</span>
                <a href="#" wire:click.prevent="setEditingEdd" class="text-blue-600 underline">Edit</a>
            @endif
        </p>

        <p>
            <b>Maturity Weeks: </b>
            <span>{{ $profile->maturity() }}</span>
        </p>
    </div>

    <div class="grid grid-cols-3 gap-x-2 gap-y-4 py-4">
        <p class="col-span-full text-xl font-semibold">Obstetric History</p>
        @unless ($obsEdit)
            <p><b>Gravida: </b> {{ $profile->gravida }}</p>
            <p><b>Parity: </b> {{ $profile->parity }}</p>

            <div class="col-span-full p-2 bg-gray-100">
                @if ($profile->obj_history)
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>Date of delivery</th>
                                <th>Duration of pregnancy</th>
                                <th>Pregnancy, Labour Puerperium</th>
                                <th>Type of delivery</th>
                                <th>Place of delivery</th>
                                <th>Baby's condition</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($profile->obj_history as $oHistory)
                                <tr>
                                    <td>{{ $oHistory['date_of_birth'] ?? '' }}</td>
                                    <td>{{ $oHistory['duration_of_pregnancy'] ?? '' }}</td>
                                    <td>{{ $oHistory['pregnancy_labour_and_puerperium'] ?? '' }}</td>
                                    <td>{{ $oHistory['type_of_delivery'] ?? '' }}</td>
                                    <td>{{ $oHistory['place_of_delivery'] ?? '' }}</td>
                                    <td>{{ $oHistory['baby_condition'] ?? '' }}</td>
                                    <td>{{ $oHistory['gender_of_baby'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No obstetric history recorded.</p>
                @endif
            </div>

            <div class="col-span-full p-2 flex flex-col gap-y-2 bg-gray-100">
                <p class="text-lg font-semibold">History of present pregnancy</p>
                @if ($profile->present_pregnancy)
                    @foreach ($profile->present_pregnancy as $k => $_v)
                        <p><b>{{ ucfirst(unslug($k)) }}:</b> {{ $_v }}</p>
                    @endforeach
                @else
                    <p>No present history of pregnancy recorded.</p>
                @endif
            </div>


            <p><b>Special consideration: </b> {{ $profile->risk_assessment }}</p>

            <div class="col-span-3">
                <a href="#" class="btn bg-primary text-white" wire:click.prevent="toggleEditObsData" class="link underline">Edit Obstetric Data</a>
            </div>
        @else
            <p><b>Gravida: </b> <input type="text" wire:model="obsData.gravida" id=""></p>
            <p><b>Parity: </b> <input type="text" wire:model="obsData.parity" id=""></p>
            <p><b>Special consideration / Risk Assessment: </b> <x-input-textarea wire:model="obsData.risk_assessment"
                    name="obsData.risk_assessment" /></p>

            <div class="col-span-3">
                <a href="#" class="btn bg-primary text-white" wire:click.prevent="updateObsData" class="link underline">Update</a>
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

    {{-- <div class="py-2">
        <p class="text-xl bold">Special consideration / Risk Assessment</p>

        <textarea name="" id="" class="form-control" rows="5" wire:model="obsData.risk_assessment">{{ $profile->risk_assessment }}</textarea>
        <div class="pt-2"></div>
        <button class="btn bg-blue-500 text-white" wire:click="updateObsData">Update Special Consideration</button>
    </div> --}}

</div>
