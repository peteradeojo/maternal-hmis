<div class="grid gap-y-4">
    {{-- @livewire('dis.prescription', ['doc' => $doc, 'type' => $type, 'id' => $id]) --}}
    <x-patient-profile :patient="$bill->patient" />

    <livewire:dis.prescription :bill="$bill" />
</div>
