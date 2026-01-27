@php
    $grandTotal = 0;
@endphp

<div>
    <p>Name: {{ $patient->name }} ({{ $patient->gender_value[0] }}) (#{{ $visit->id }})</p>
    <p>Date: {{ $visit->created_at->format('Y-m-d h:i A') }}</p>
</div>

@if ($visit->type == 'Antenatal')
    <p><b>Card Type:</b> {{ $visit->patient->anc_profile->card_type }}</p>
    <p class="text-xl mt-3 bold">Note</p>
    <p>Next visit: {{ $visit->return_visit }}</p>
@endif

<livewire:records.bill-report :visit="$visit" />
