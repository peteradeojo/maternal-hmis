<div>
    <div class="card py px mb-2">
        <input type="search" wire:model="patientId" placeholder="Enter Booking ID">
        <button wire:click='load' class="btn bg-blue-400 text-white">Click</button>

        @if ($profile)
            <div class="my" wire:loading.remove>
                <p><b>Booking:</b> {{ $profile->patient->name }}</p>
                <form action="{{ route('nurses.submit-anc-booking', $profile) }}" method="post">
                    @csrf
                    @include('nursing.components.vitals-form')
                    <div class="py-2"></div>

                    @livewire('lmp-form')

                    <div class="grid gap-x-3 grid-cols-4">
                        <div class="form-group">
                            <label for="">Gravidity</label>
                            <input type="number" name="gravida" required value="0" />
                        </div>

                        <div class="form-group">
                            <label for="">Parity</label>
                            <input type="number" name="parity" required />
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
