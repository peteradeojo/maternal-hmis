<div class="grid grid-cols-2">
    <p class="text-lg font-semibold col-span-full pb-2">{{ $patient->name }} #{{ $patient->card_number }}</p>
    <p><b>Category:</b> {{ $patient->category->name }}</p>
    <p><b>Gender:</b> {{ $patient->gender }}</p>
    <p><b>Phone number:</b> {{ $patient->phone }}</p>
    <p><b>Date of birth:</b> {{ $patient->dob?->format('Y-m-d') }}</p>
    <p>
        <b>Insurance:</b>
        <span>{{ $patient->insurance[0]->hmo_name ?? 'No insurance' }}</span>
        {{-- <b>{{ isset($patient->insurance[0]) ? strtoupper("({$patient->insurance[0]->status})") : '' }}</b> --}}
    </p>
</div>
{{ $slot }}
