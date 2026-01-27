@props(['color' => 'bg-gray-100', 'note'])

<div class="{{ $color }} p-2">
    <p>@nl2br($note->note)</p>
    <p class="text-xs font-semibold">{{ $note->recorder?->recorder ?? $note->consultant->name }}</p>

    <div class="flex-center justify-between text-xs">
        <p class="text-red-700">{{ $note->created_at }}</p>
        @can('delete', $note)
            @if (is_subclass_of(static::class, \Livewire\Component::class))
                <button wire:click="@(removeNote({{ $note->id }}))" class="text-red-700">Delete</button>
            @endif
        @endcan
    </div>
</div>
