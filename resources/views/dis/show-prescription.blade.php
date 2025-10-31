{{-- @extends('layouts.app')
@section('title', 'Prescription:: ' . $doc->patient?->name) --}}

{{-- @section('content') --}}
@isset($doc->patient)
    <div class="py-1">
        <p><b>Name: </b>{{ $doc->patient->name }}</p>
        <p><b>Gender: </b>{{ $doc->patient->gender_value }}</p>
        <p><b>Date: </b> {{ $doc->created_at->format('Y-m-d H:i A') }}</p>
    </div>
    <div class="p-1">
        <div class="py-1">
            @livewire('dis.prescription', ['doc' => $doc, 'type' => $type, 'id' => $id])
        </div>
    </div>
@else
    <div class="card h-48 grid place-items-center">
        <p class="text-3xl font-semibold text-red-500">Oops!</p>
        <p class="text-lg">For some reason, you're unable to view this prescription. Please reach out to IT.</p>
    </div>
@endisset
{{-- @endsection --}}
