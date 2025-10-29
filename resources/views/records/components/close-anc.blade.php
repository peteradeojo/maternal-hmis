<div>
    @php
        $patient = $profile->patient;
    @endphp

    @unless ($patient->visits->count() > 0 && $patient->visits[0]->status == Status::active->value)
        <form action="{{ route('records.close-anc', $profile) }}" method="post">
            @method('DELETE')
            @csrf

            <div class="form-group">
                <label>Patient: </label>
                <input type="text" class="form-control" readonly="readonly" value="{{ $profile->patient->name }}" />
            </div>
            <div class="form-group">
                <label>Date closed:</label>
                <input type="datetime-local" name="closed_on" class="form-control" required />
            </div>
            <div class="form-group">
                <label>Reason</label>
                <textarea name="close_reason" cols="30" rows="10" class="form-control" required></textarea>
            </div>
            <div class="flex justify-end">
                <button class="btn bg-red-500 text-white">Close <i class="fa fa-trash"></i></button>
            </div>
        </form>
    @else
        <p>This patient still has an ongoing visit. End the consultation before closing the antenatal profile.</p>
    @endunless

</div>
