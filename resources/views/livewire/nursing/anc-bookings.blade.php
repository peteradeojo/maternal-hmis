<div>
    <div class="card py px mb-2">
        <input type="search" wire:model="patientId" placeholder="Enter Booking ID">
        <button wire:click='load'>Click</button>

        @if ($profile)
            <div class="my" wire:loading.remove>
                <p><b>Booking:</b> {{ $profile->patient->name }}</p>
                <form action="{{ route('nurses.submit-anc-booking', $profile) }}" method="post">
                    @csrf
                    @include('nursing.components.vitals-form')
                    <div class="form-group">
                        <button class="btn btn-blue">Submit</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
