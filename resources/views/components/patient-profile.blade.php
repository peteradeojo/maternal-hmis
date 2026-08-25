<div class="grid sm:grid-cols-2">
    <p class="text-lg font-semibold col-span-full pb-2">{{ $patient->name }} #{{ $patient->card_number }}</p>
    <p><b>Category:</b> {{ $patient->category->name }}</p>
    <p><b>Gender:</b> {{ $patient->gender }}</p>
    <p><b>Phone number:</b> {{ $patient->phone }}</p>
    <p><b>Date of birth:</b> {{ $patient->dob?->format('Y-m-d') }}</p>
    <p><b>Age: </b> {{ $patient->getAge() }}</p>
    @if ($insurance = $patient->insurance()->whereIn('status', [Status::active, Status::pending])->first())
        <p>
            <b>Insurance:</b>
            <span>{{ $insurance->hmo_name ?? 'No insurance' }}</span>
        </p>
        <p>
            <b>Insurance number:</b>
            <span>{{ $insurance->hmo_id_no ?? 'No insurance' }}</span>
        </p>
    @endif
</div>
{{ $slot }}
