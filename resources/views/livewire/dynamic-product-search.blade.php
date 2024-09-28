<div>
    {{-- The whole world belongs to you. --}}
    <div class="relative">
        <input type="search" wire:model.debounce.2s="queryString" wire:keyup.debounce.250ms="searchProducts"
            wire:keydown.enter="select({{ null }})" class="w-full relative"
            wire:focus.debounce.250ms="searchProducts" placeholder="Enter Item" />
        @if ($display && count($results ?? []) > 0)
            <ul class="relative bottom-0 border border-black sp-list max-h-80 overflow-y-auto w-2/3">
                <li class="hover:bg-gray-200 cursor-pointer font-bold text-gray-600">Click an item to Select</li>
                @forelse ($results  ?? [] as $result)
                    <li class="hover:bg-gray-200 cursor-pointer" wire:click="select({{ $result }})" @click.stop>
                        {{ $result->name }}</li>
                @empty
                    <li>No Result found</li>
                @endforelse
        @endif
        </ul>
    </div>
</div>
