<div>
    <div>
        <input type="search" wire:model="patientId" placeholder="Enter Booking ID">
        <button wire:click='load'>Load Booking Form</button>
    </div>

    @if ($profile)
        <div class="my" wire:loading.remove wire:target='load'>
            <p><b>Booking:</b> {{ $profile->patient->name }}</p>
            <form action="{{ route('lab.submit-anc-booking', $profile) }}" method="post">
                @csrf
                <div class="row">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="completed"> Booking Completed</label>
                    <br>
                    <button class="btn btn-blue">Submit</button>
                </div>
            </form>
        </div>
    @endif
</div>
