<div class="grid grid-cols-2">
    <p class="text-lg font-semibold col-span-full pb-2">{{ $patient->name }} #{{ $patient->card_number }}</p>
    <p><b>Category:</b> {{ $patient->category->name }}</p>
    <p><b>Gender:</b> {{ $patient->gender }}</p>
    <p><b>Phone number:</b> {{ $patient->gender }}</p>
    <p><b>Date of birth:</b> {{ $patient->gender }}</p>
    {{-- <p><b>Insurance: {{ $patient->insurance }}</b></p> --}}

    @if (isset($expanded) && $expanded)
    @endif
</div>
