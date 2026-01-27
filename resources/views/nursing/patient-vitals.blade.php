{{-- @extends('layouts.app')

@section('content') --}}
<div class="container">
    <div class="card py px">
        <div class="my">
            <h2>{{ $visit->patient->name }}</h2>
            <h3>{{ $visit->patient->card_number }}</h3>
        </div>

        <livewire:nurses.vitals :event="$visit" wire:saved="$refresh" />
    </div>
</div>
{{-- @endsection --}}
