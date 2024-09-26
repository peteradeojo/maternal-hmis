<div>
    {{-- The whole world belongs to you. --}}
    <div class="relative border">
        <input type="search" wire:model.live="queryString" wire:keyup.debounce.250ms="searchProducts"
            class="w-full relative" wire:blur.debounce.1s="resetResults" wire:focus.debounce.250ms="searchProducts"
            placeholder="Enter Item" />
        @if ($display && count($results ?? []) > 0)
            <ul class="relative bottom-0 border border-black sp-list max-h-80 overflow-y-auto">
                @forelse ($results  ?? [] as $result)
                    <li class="hover:bg-gray-200 cursor-pointer" wire:click="select({{ $result->id }})">
                        {{ $result->name }}</li>
                @empty
                    <li>No Result found</li>
                @endforelse
        @endif
        </ul>
    </div>
</div>
