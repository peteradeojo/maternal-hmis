<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <div>
        <div class="grid grid-cols-3">
            <div class="form-group">
                <p>Gravidity</p>
                <input type="text" wire:model="bookingForm.gravida" required />
                @error('bookingForm.gravida')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <p>Parity</p>
                <input type="text" wire:model="bookingForm.parity" required />
            </div>
            <div class="form-group">
                <p>Presentation</p>
                <input type="text" wire:model="bookingForm.presentation" required />
            </div>
            <div class="form-group">
                <p>Lie</p>
                <input type="text" wire:model="bookingForm.lie" required />
            </div>
            <div class="form-group">
                <p>Height of Fundus</p>
                <input type="text" wire:model="bookingForm.fundal_height" required />
            </div>
            <div class="form-group">
                <p>Fetal Heart Rate</p>
                <input type="text" wire:model="bookingForm.fetal_heart_rate" required />
            </div>
            <div class="form-group">
                <p>Relationship of Presenting Part to Pelvis</p>
                <input type="text" wire:model="bookingForm.presentation_relationship" required />
            </div>
        </div>
        <button wire:click="submitBooking" class="btn btn-blue">Submit</button>
    </div>

    <div class="py-1"></div>

    <p>Tests</p>
    <livewire:dynamic-product-search :departmentId=5 @selected="addTest($event.detail)" />

    <div class="p-1 bg-gray-100">
        @include('doctors.components.test-results', ['tests' => $profile->tests])
    </div>

    <p>Treatments</p>
    <livewire:dynamic-product-search :departmentId=4 @selected="addPrescription($event.detail.id)" />

    <div class="p-1 bg-gray-100 text-sm">

    </div>
</div>
