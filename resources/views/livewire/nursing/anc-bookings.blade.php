<div>
    <div class="card py px mb-2">
        <input type="search" wire:model="patientId" placeholder="Enter Booking ID" wire:keydown.enter="load">
        <button wire:click='load' class="btn bg-blue-400 text-white">Click</button>

        @if ($profile)
            <div class="my" wire:loading.remove>
                <p><b>Booking:</b> {{ $profile->patient->name }}</p>
                <form action="{{ route('nurses.submit-anc-booking', $profile) }}" method="post">
                    @csrf
                    @include('nursing.components.vitals-form', ['profile' => $profile])
                    <div class="py-2"></div>

                    @livewire('lmp-form', ['profile' => $profile], key($profile->id))

                    <div class="grid gap-x-3 grid-cols-4">
                        <div class="form-group col-span-2">
                            <label for="">Gravidity</label>
                            <input type="number" class="form-control" name="gravida" required value="0" />
                        </div>

                        <div class="form-group col-span-2">
                            <label for="">Parity</label>
                            <input type="number" name="parity" class="form-control" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-blue">Submit</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
